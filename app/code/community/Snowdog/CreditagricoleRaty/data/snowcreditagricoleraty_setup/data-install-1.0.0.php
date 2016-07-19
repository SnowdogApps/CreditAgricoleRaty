<?php

$pageContent = <<<EOD

<img src="https://ewniosek.credit-agricole.pl/eWniosek/res/CA_grafika/pion_raty_przezroczyste.png" />
<h2>Skorzystaj z opcji płatności Credit Agricole Raty i wybieraj to, co najlepsze.</h2>
<p><strong>Przekonaj się  jakie to proste:</strong></p>
<ul>
<li><strong>przy każdym towarze</strong>, który może być kupiony na CA Raty - <strong>sprawdź wysokość raty</strong></li>
<li>gdy kupujesz więcej rzeczy, <strong>za całe zakupy zapłacisz w ratach - wygodnie</strong></li>
<li><strong>z rat skorzystasz do max. kwoty 10 000 zł</strong></li>
<li><strong>samodzielnie dobierzesz ilość rat i ich wysokość</strong>, tak by spłata nie była dla Ciebie kłopotliwa</li>
<li><strong>szybko i bez zbędnych formalności wypełnisz wniosek</strong>, bez wychodzenia z domu</li>
<li><strong>umowę i zakupy dostaniesz do domu</strong></li>
</ul>
<h2>Sprawdź!</h2>
<p>
{{config path="general/store_information/name"}} oświadcza, iż na podstawie zawartych umów z Credit Agricole Bank Polska S.A.
jest upoważniony do wykonywania czynności prawnych zwąiązanych z zawarciem/zmianą umów kredytu na zakup towarów i usług.</p>


EOD;

$privacyPageData = array(
    'title'           => 'Płatności/Raty',
    'content_heading' => 'Płatności/Raty',
    'root_template'   => 'one_column',
    'identifier'      => 'payment-instalments',
    'content'         => $pageContent,
    'is_active'       => 1,
    'stores'          => array(0),
    'sort_order'      => 0
);

Mage::getModel('cms/page')->setData($privacyPageData)->save();

$footerLinksBlock = Mage::getModel('cms/block')->load('footer_links','identifier');

if ($footerLinksBlock->getId()) {
    $content = $footerLinksBlock->getContent();
    if (preg_match('/<ul>(.*?)<\\/ul>/ims',$content, $matches)) {
        $content = preg_replace('/<li class="last privacy">/ims', '<li>',$content);
        $replacment = '<li class="last installment">'
            . "<a href=\"{{store direct_url=\"payment-instalments\"}}\">"
            . "Płatności/Raty</a></li>\r\n</ul>";
        $content = preg_replace('/<\\/ul>/ims', $replacment, $content);
        $footerLinksBlock->setContent($content)->save();
    }
}