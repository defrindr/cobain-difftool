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
        foreach ($origin as $originKey => $originValue) {
            foreach ($modified as $modifiedKey => $modifiedValue) {
                $sameValue = ($originValue === $modifiedValue);
                $sameKey = ($originKey === $modifiedKey);
                if ($sameValue && $sameKey) {
                    $hasReplaced = true;

                    unset($modified[$modifiedKey]);
                    unset($origin[$originKey]);
                    continue;
                } else if ($sameKey && !$sameValue) {
                    // remove duplicate word in same line
                    foreach ($originValue as $originWordKey => $originWordValue) {
                        foreach ($modifiedValue as $modifiedWordKey => $modifiedWordValue) {
                            $sameWordKey = ($originWordKey === $modifiedWordKey);
                            $sameWordValue = ($originWordValue === $modifiedWordValue);
                            if ($sameWordKey && $sameWordValue) {
                                unset($origin[$originKey][$originWordKey]);
                                unset($modified[$modifiedKey][$modifiedWordKey]);
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

    public function arrayToString($source, $withLine = false)
    {
        if($withLine){
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
        foreach ($origin as $originKey => $originValue) {
            if (empty($originValue)) {
                unset($origin[$originKey]);

                foreach ($modified as $modifiedKey => $modifiedValue) {
                    foreach ($modifiedValue as $modifiedWordKey => $modifiedWordValue) {
                        $modified[$modifiedKey][$modifiedWordKey] = self::setColor($modifiedWordValue, 'green');
                    }
                }
            } else {

                foreach ($modified as $modifiedKey => $modifiedValue) {
                    foreach ($originValue as $originWordKey => $originWordValue) {
                        foreach ($modifiedValue as $modifiedWordKey => $modifiedWordValue) {
                            $origin[$originKey][$originWordKey] = self::setColor($originWordValue, 'red');
                            $modified[$modifiedKey][$modifiedWordKey] = self::setColor($modifiedWordValue, 'green');
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

        $sourceOrigin = self::textToArray($source['origin']);
        $sourceModified = self::textToArray($source['modified']);

        $sourceOrigin = self::assignArray($sourceOrigin, $diff['origin']);
        $sourceModified = self::assignArray($sourceModified, $diff['modified']);

        $textOrigin = self::arrayToString($sourceOrigin, $withLine = True);
        $textModified = self::arrayToString($sourceModified, $withLine = true);

        return [
            "origin" => $textOrigin,
            "modified" => $textModified,
        ];
    }

    public function compare($originText, $modifiedText, $key = true)
    {
        $origin = self::textToArray($originText);
        $modified = self::textToArray($modifiedText);

        $hasReplaced = false;
        $diff = [];

        $diff = self::recursive($origin, $modified);
        $diff = self::buildToHTML($diff['origin'], $diff['modified'], [
            'origin' => $originText,
            'modified' => $modifiedText
        ]);

        return $diff;
    }
}
