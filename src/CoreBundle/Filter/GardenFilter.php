<?php

namespace CoreBundle\Filter;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class GardenFilter extends DateFilter
{
    const NAME = 'name';
    const ZIP_CODE = 'zip_code';
    const OWNER = 'owners';
    const RADIUS = 'radius';
    const LAT = 'latitude';
    const LNG = 'longitude';

    const DEFAULT_RADIUS = 5000;

    private $owners;
    private $zipCodes;

    public function __construct(\Symfony\Component\Translation\DataCollectorTranslator $translator, EntityRepository $repo)
    {
        parent::__construct($translator);

        $this->repo = $repo;
        $this->owners = [];
        $this->zipCodes = [];
    }

    protected function buildParams()
    {
        parent::buildParams();

        if ($this->has(self::OWNER)) {
            $owner = $this->get(self::OWNER);

            if (!is_array($owner)) {
                $this->owners[] = $owner;
            } else {
                $this->owners = $owner;
            }
        }

        if ($this->has(self::ZIP_CODE)) {
            $zipcode = $this->get(self::ZIP_CODE);

            if (!is_array($zipcode)) {
                $this->zipCodes[] = $zipcode;
            } else {
                $this->zipCodes = $zipcode;
            }
        }
    }

    public function isValid()
    {
        $isValid = parent::isValid();

        if (!$isValid) {
            return false;
        }

        if ($this->has(self::LAT) xor $this->has(self::LNG)) {
            $this->error = $this->translator->trans("core.filter.bad_geoloc");
            return false;
        }

        $lng = $this->get(self::LNG);
        $lat = $this->get(self::LAT);

        if (!is_null($lng)) {
            if ($lng > 180 || $lng < -180) {
                $this->error = $this->translator->trans("core.filter.lng_out_of_range");
                return false;
            }
        }

        if (!is_null($lat)) {
            if ($lat > 90 || $lat < -90) {
                $this->error = $this->translator->trans("core.filter.lat_out_of_range");
                return false;
            }
        }

        return true;
    }

    public function filter(QueryBuilder $qb)
    {
        $qb = parent::filter($qb);

        if ($this->has(self::NAME)) {
            $name = $this->like(self::NAME);

            $qb->andWhere($this->alias . 'name LIKE :name')
                ->setParameter('name', $name);
        }

        if ($this->has(self::OWNER)) {
            $qb->leftJoin($this->alias . 'owner', 'o')
                ->andWhere('o.username IN (:owners)')
                ->setParameter('owners', $this->owners);
        }

        if ($this->has(self::ZIP_CODE)) {
            $qb->andWhere($this->alias . 'zipCode IN (:zipcodes)')
                ->setParameter('zipcodes', $this->zipCodes);
        }

        if ($this->has(self::LAT)) {
            $lng = $this->get(self::LNG);
            $lat = $this->get(self::LAT);
            $radius = $this->get(self::RADIUS, self::DEFAULT_RADIUS) / 1000;
            $qb->addSelect('GEO_DISTANCE(:latOrigin, :lngOrigin, ' . $this->alias . 'latitude, ' . $this->alias . 'longitude) AS HIDDEN distance')
                ->having('distance <= :radius')
                ->setParameter('latOrigin', $lat)
                ->setParameter('lngOrigin', $lng)
                ->setParameter('radius', $radius);
        }

        return $qb;
    }

    protected function getFields()
    {
        $fields = parent::getFields();

        return array_merge([self::NAME, 'zipCode', 'city'], $fields);
    }
}
