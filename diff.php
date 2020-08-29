<?php

class Diff
{

    public $redColor = "<div class='red'>[text]</div>";
    public $greenColor = "<div class='geen'>[text]</div>";

    public function textToArray($text)
    {
        $listWord = [];

        $lines = explode("\n", $text);
        foreach ($lines as $lineKey => $line) {
            $listWord[$lineKey] = [];

            $words = explode(" ", $line);
            foreach ($words as $wordKey => $word) {
                $listWord[$lineKey] += [$wordKey => $word];
            }
        }

        return $listWord;
    }

    public function hasIndex($array, $index)
    {
        if (empty($array[$index])) {
            return false;
        }
        return true;
    }

    public function recursive($origin, $modified)
    {
        $hasReplaced = false;

        // remove duplicate line
        foreach ($origin as $key_origin => $value_origin) {
            foreach ($modified as $key_modified => $value_modified) {
                $sameValue = ($value_origin === $value_modified);
                $sameKey = ($key_origin === $key_modified);
                if ($sameValue && $sameKey) {
                    $hasReplaced = true;

                    unset($modified[$key_modified]);
                    unset($origin[$key_origin]);
                    continue;
                } else if ($sameKey && !$sameValue) {
                    // remove duplicate word in same line
                    foreach ($value_origin as $key_nested_origin => $value_nested_origin) {
                        foreach ($value_modified as $key_nested_modified => $value_nested_modified) {
                            $sameWordKey = ($key_nested_origin === $key_nested_modified);
                            $sameWordValue = ($value_nested_origin === $value_nested_modified);
                            if ($sameWordKey && $sameWordValue) {
                                unset($origin[$key_origin][$key_nested_origin]);
                                unset($modified[$key_modified][$key_nested_modified]);
                            }
                        }
                    }
                }
            }

            if ($hasReplaced) {
                $hasReplaced = false;
                continue;
            }
        }

        return [
            "origin" => $origin,
            "modified" => $modified,
        ];
    }

    public function setColor($text, $style)
    {
        $out = "";
        switch ($style) {
            case 'red':
                $out = str_replace('[text]', $text, "<div class='red'>[text]</div>");
                break;
            case 'green':
                $out = str_replace('[text]', $text, "<div class='green'>[text]</div>");
                break;
            default:
                throw new Exception("\$style doesnt exist.", 1);
                break;
        }
        // die();
        return $out;
    }

    public function arrayToString($source, $with_line = false)
    {
        if($with_line){
            $text = "";
            foreach ($source as $lines) {
                $line = "";
                foreach ($lines as $word) {
                    $line .= "$word ";
                }
                $line = trim($line);
                $text .= "$line\n";
            }
            $text = trim($text);
    
            return $text;
        }else{
            $text = "";
            foreach ($source as $word) {
                $text .= "$word ";
            }
    
            $text = trim($text);
    
            return $text;
        }
    }

    public function colorize($origin, $modified)
    {
        foreach ($origin as $key_origin => $value_origin) {
            if (empty($value_origin)) {
                unset($origin[$key_origin]);

                foreach ($modified as $key_modified => $value_modified) {
                    foreach ($value_modified as $key_nested_modified => $value_nested_modified) {
                        $modified[$key_modified][$key_nested_modified] = self::setColor($value_nested_modified, 'green');
                    }
                }
            } else {

                foreach ($modified as $key_modified => $value_modified) {
                    foreach ($value_origin as $key_nested_origin => $value_nested_origin) {
                        foreach ($value_modified as $key_nested_modified => $value_nested_modified) {
                            $origin[$key_origin][$key_nested_origin] = self::setColor($value_nested_origin, 'red');
                            $modified[$key_modified][$key_nested_modified] = self::setColor($value_nested_modified, 'green');
                        }
                    }
                }
            }
        }

        return [
            "origin" => $origin,
            "modified" => $modified,
        ];
    }

    function assignArray($source, $values){
        foreach($values as $keyLine => $valueLine ){
            foreach ($valueLine as $keyWord => $valueWord) {
                $source[$keyLine][$keyWord] = $valueWord;
            }
        }

        return $source;
    }

    public function buildToHTML($origin, $modified, $source){
        $diff = self::colorize($origin, $modified);

        $source_origin = self::textToArray($source['origin']);
        $source_modified = self::textToArray($source['modified']);

        $source_origin = self::assignArray($source_origin, $diff['origin']);
        $source_modified = self::assignArray($source_modified, $diff['modified']);

        $origin_text = self::arrayToString($source_origin, $with_line = True);
        $modified_text = self::arrayToString($source_modified, $with_line = true);

        return [
            "origin" => $origin_text,
            "modified" => $modified_text,
        ];
    }

    public function combineArray($origin, $combinator, $result = []){
        foreach($combinator as $key_combinator => $value_combinator){
            foreach($origin as $key_origin => $value_origin){
                if($key_origin == $key_combinator){
                    if(gettype($value_combinator) == "array"){
                        foreach($combinator as $key => $value){
                            $result[$key_origin] = self::combineArray($value_origin, $value_combinator, $result);
                        }
                    }else{
                        $result[$key_origin] = self::setColor($value_origin, 'red') . self::setColor($value_combinator, 'green');
                    }
                }else{
                    $result[$key_origin] = $value_origin;
                }
            }
        }

        return $result;
    }

    public function compare($origin_text, $modified_text, $combine_string = false)
    {
        $origin = self::textToArray($origin_text);
        $modified = self::textToArray($modified_text);

        $hasReplaced = false;
        $diff = [];

        $diff = self::recursive($origin, $modified);
        

        if($combine_string){
            // return $modified;
            $result = self::combineArray($origin, $diff['modified']);
            return self::arrayToString($result, $with_line = true);
        }

        $diff = self::buildToHTML($diff['origin'], $diff['modified'], [
            'origin' => $origin_text,
            'modified' => $modified_text
        ]);

        return $diff;
    }
}
