<?php

class Snowdog_CreditagricoleRaty_Model_Options_ImagesCalculate extends Snowdog_CreditagricoleRaty_Model_Options_Image {

    public function toOptionArray() {

        $images = parent::toOptionArray();
        $imagesCalculate = array(
            array(
                'value' => 'https://ewniosek.credit-agricole.pl/eWniosek/res/CA_grafika/raty_485_duckblue.png',
                'label' => 'oblicz raty 485pix kolor'),
            array(
                'value' => 'https://ewniosek.credit-agricole.pl/eWniosek/res/CA_grafika/raty_485_gray.png',
                'label' => 'oblicz raty 485pix szary'),
            array(
                'value' => 'https://ewniosek.credit-agricole.pl/eWniosek/res/CA_grafika/raty_485_przezroczysty.png',
                'label' => 'oblicz raty 485pix przezroczysty'),
            array(
                'value' => 'https://ewniosek.credit-agricole.pl/eWniosek/res/CA_grafika/oblicz_raty_duckblue.png',
                'label' => 'oblicz raty kolor'),
            array(
                'value' => 'https://ewniosek.credit-agricole.pl/eWniosek/res/CA_grafika/oblicz_raty_gray.png',
                'label' => 'oblicz raty szary'),
            array(
                'value' => 'https://ewniosek.credit-agricole.pl/eWniosek/res/CA_grafika/oblicz_raty_przezroczysty.png',
                'label' => 'oblicz raty przezroczysty'),
            array(
                'value' => 'https://ewniosek.credit-agricole.pl/eWniosek/resources/swf/rata_dowolna_146x28_blue.swf',
                'label' => 'Flash 146x28 niebieski- Rata __ zł'),
            array(
                'value' => 'https://ewniosek.credit-agricole.pl/eWniosek/resources/swf/rata_dowolna_146x28_gray.swf',
                'label' => 'Flash 146x28 szary - Rata __ zł'),
            array(
                'value' => 'https://ewniosek.credit-agricole.pl/eWniosek/resources/swf/rata_dowolna_370x75_blue.swf',
                'label' => 'Flash 370x75 niebieski- Rata __ zł'),
            array(
                'value' => 'https://ewniosek.credit-agricole.pl/eWniosek/resources/swf/rata_dowolna_370x75_gray.swf',
                'label' => 'Flash 370x75 szary- Rata __ zł')
        );


        return array_merge($imagesCalculate, $images);

    }
}