<?php

class Product
{

    public function  __construct($url)
    {
        $this->url = $url;
    }

    public $name;
    public $price;

    public $producer;
    public $id_producer;
    public $categories;

    public $description;
    public $description_short;
    public $meta_description;
    public $meta_tags;

    //import product always not visible
    public $weight;
    public $photos = array();
    public $visible = 0;
    public $url;
    public $available = true;
    public $out_of_stock = 2;
    public $code;
    public $height;
    public $depth;
    public $width;
    public $discount;

    //  public $quantity;


    public function setName($suffix, $notavailable)
    {

        if ($this->available)
            $notavailable = null;

        $promo = $this->discount ? "PROMO" : null;
        $this->name = implode(' ', array_filter(array($this->name, $suffix, $promo, $notavailable)));
    }

    public function SetDescription($description)
    {
        //remove html attributes from tags;c
        $description = preg_replace("#(<[a-zA-Z0-9]+)[^\>]+>#", "\\1>", $description);
        $description = html_entity_decode(strip_tags($description, '<b><p><strong><b><ul><ol><li><h1><h2><h3><h4><h5><br>'));
        $description = $this->html_entity_decode_wthsemicolon($description, '<b><p><strong><b><ul><ol><li>');
#htmlentities
        $this->description = trim($description);

    }

    public function setShortDescription()
    {

        $breaks = array('<br />', '<br>', '<br/>');
        $this->description_short = Text::limit_chars(strip_tags(str_ireplace($breaks, ' ', $this->description)), 400, true);
        $this->meta_description = Text::limit_chars($this->description_short, 160, true);

    }


    public function prepareCode()
    {
        //   print_r($this->name);
        //  var_dump($this->producer);
        $t = substr($this->producer, 0, 3);
        $t1 = str_ireplace(strtolower($this->producer), '', strtolower($this->name));

        $this->code = substr(strtoupper(\URL::title($t . $t1, '')), 0, 20);
    }

    private function html_entity_decode_wthsemicolon($description)
    {
        $mapping = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES | ENT_HTML5, 'UTF-8');

// change array values representing the entities to regex pattern with negativ lookahead for semicolon
        array_walk($mapping, function (&$value) {
            $value = '/' . rtrim($value, ';') . '(?!;)/';
        });
// replace all entities without semicolon by their utf8 representation
        return preg_replace(array_values($mapping), array_keys($mapping), $description);

    }


}