<?php

namespace App\Mapper;

use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use App\Dto\TripDto;
use App\Entity\Trip;
use App\Entity\Etape;

class TripMapper
{
    private PropertyInfoExtractorInterface $propertyInfo;

    public function __construct()
    {
        $this->propertyInfo = new PropertyInfoExtractor(
            [new ReflectionExtractor()],
            [new PhpDocExtractor()]
        );
    }

    public function mapDtoToEntity(TripDto $tripDto, Trip $trip): Trip
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($this->propertyInfo->getProperties(TripDto::class) as $property) {
            $value = $accessor->getValue($tripDto, $property);

            if ($property === 'etapes') {
                // Handle array mapping separately
                $this->mapEtapes($value, $trip);
            } elseif ($this->propertyInfo->getTypes(Trip::class, $property)) {
                $accessor->setValue($trip, $property, $value);
            }
        }

        return $trip;
    }

    public function mapEntityToDto(Trip $trip): TripDto
    {
        $tripDto = new TripDto();
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($this->propertyInfo->getProperties(TripDto::class) as $property) {
            if ($property === 'etapes' && $this->propertyInfo->isReadable(Trip::class, $property)) {
                // Handle array mapping separately
                $this->mapEtapesToDto($trip, $tripDto);
            } elseif ($this->propertyInfo->getTypes(TripDto::class, $property)) {
                $accessor->setValue($tripDto, $property, $accessor->getValue($trip, $property));
            }
        }

        return $tripDto;
    }

    public function mapEtapes(array $etapesDto, Trip $trip): void
    {
        foreach ($etapesDto as $etapeDto) {
            if (
                isset($etapeDto['adresse_depart']) &&
                isset($etapeDto['code_postal_depart']) &&
                isset($etapeDto['ville_depart']) &&
                isset($etapeDto['adresse_arrivee']) &&
                isset($etapeDto['code_postal_arrivee']) &&
                isset($etapeDto['ville_arrivee'])
            ) {
                $etape = new Etape();
                $etape->setAdresseDepart($etapeDto['adresse_depart']);
                $etape->setCodePostalDepart($etapeDto['code_postal_depart']);
                $etape->setVilleDepart($etapeDto['ville_depart']);
                $etape->setAdresseArrivee($etapeDto['adresse_arrivee']);
                $etape->setCodePostalArrivee($etapeDto['code_postal_arrivee']);
                $etape->setVilleArrivee($etapeDto['ville_arrivee']);
    
                // Link the Etape to the Trip using the inverse side of the relationship
                $trip->addEtape($etape);
    
                // Log the values for debugging
                VarDumper::dump($etapeDto);
            }
        }
    }
    
    

    private function mapEtapesToDto($etapes, TripDto $tripDto): void
    {
        $etapesDto = [];

        foreach ($etapes as $etape) {
            $etapesDto[] = [
                'id' => $etape->getId(),
                'adresse_depart' => $etape->getAdresseDepart(),
                'code_postal_depart' => $etape->getCodePostalDepart(),
                'ville_depart' => $etape->getVilleDepart(),
                'adresse_arrivee' => $etape->getAdresseArrivee(),
                'code_postal_arrivee' => $etape->getCodePostalArrivee(),
                'ville_arrivee' => $etape->getVilleArrivee(),
            ];

            // Log the values for debugging
            VarDumper::dump($etapesDto);
        }

        $tripDto->setEtapes($etapesDto);
    }
    
}
