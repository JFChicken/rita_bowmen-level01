<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ColorItemsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        /*
         * Layer type is build off of the number of layers that we are working with
         * so :
         * seven layers
         * base
         * eye color
         * eye white
         * effect01
         * effect02
         * effect03
         * lines
         */
        $layers = [
            "base",
            "eye color",
            "eye white",
            "effect01",
            "effect02",
            "effect03",
            "lines",
        ];
        return [
            'name' => $this->faker->colorName(),
            'hexColor' => $this->faker->hexColor(),
            'layerType' => $layers[rand(0, count($layers)-1)],
            'meta'=>json_encode($this->faker->jobTitle()),
        ];
    }

}
