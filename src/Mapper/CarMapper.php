<?php

namespace App\Mapper;

use App\Dto\CarDto;
use App\Entity\Car;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;

class CarMapper
{
    private PropertyInfoExtractorInterface $propertyInfoExtractor;
    private $propertyAccessor;

    public function __construct()
    {
        $reflectionExtractor = new ReflectionExtractor();
        $phpDocExtractor = new PhpDocExtractor();

        $this->propertyInfoExtractor = new PropertyInfoExtractor(
            [$reflectionExtractor],
            [$phpDocExtractor, $reflectionExtractor, $phpDocExtractor]
        );

        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function mapDtoToEntity(CarDto $carDto, Car $car): Car
    {
        $properties = $this->propertyInfoExtractor->getProperties(\get_class($carDto));

        foreach ($properties as $property) {
            $value = $this->propertyAccessor->getValue($carDto, $property);
            $setter = 'set' . ucfirst($property);

            if (method_exists($car, $setter)) {
                $car->$setter($value);
            }
        }

        return $car;
    }

    public function mapEntityToDto(Car $car, CarDto $carDto): CarDto
    {
        $properties = $this->propertyInfoExtractor->getProperties(\get_class($car));

        foreach ($properties as $property) {
            $value = $this->propertyAccessor->getValue($car, $property);
            $setter = 'set' . ucfirst($property);

            if (method_exists($carDto, $setter)) {
                $carDto->$setter($value);
            }
        }

        return $carDto;
    }
}
