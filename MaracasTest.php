#!/usr/bin/php7.3
<?php

/**
 * Validation of input parameters
 */
$source = isset($argv[1]) ? $argv[1] : exit("Source not specified. \n");

/**
 * Class MaracasTest
 *
 *  Get XML from Source, parse and save into .csv file
 */
class MaracasTest
{
    /**
     * @var
     */
    private $source;

    /**
     * MaracasTest constructor.
     * @param $source
     */
    public function __construct($source)
    {
        $this->source = $source;
    }

    /**
     *
     */
    public function init()
    {
        $rss =  simplexml_load_file($this->source);
        $fields = [];
        foreach ($rss->channel->item as $item) {
            $fields['url'][] = $item->link;
            foreach ($item->children('amzn', true)->products as $products) {
                foreach ($products as $product) {
                    $fields['asin'][] = substr($product->productURL, -11, -1);
                    $fields['product_url'][] = $product->productURL;
                    $fields['product_headline'][] = strip_tags($product->productHeadline);
                    if($product->introtext) {
                        $fields['introtext'][] = strip_tags($product->introtext);
                    }
                    if($product->award) {
                        $fields['award'][] = strip_tags($product->award);
                    }
                    if($product->productSummary) {
                        $fields['product_summary'][] = strip_tags($product->productSummary);
                    }
                }
            }
        }

        if(!empty($fields['introtext'])) {
            $fields['introtext_count'][] = count($fields['introtext']);
        } else {
            $fields['introtext_count'][] = 'Field introtext not found';
        }

        if(!empty($fields['award'])) {
            $fields['award_count'][] = count($fields['award']);
        } else {
            $fields['award_count'][] = 'Field award not found';
        }

        if(!empty($fields['product_summary'])) {
            $fields['product_summary_count'][] = count($fields['product_summary']);
        } else {
            $fields['product_summary_count'][] = 'Field productSummary not found';
        }

        $this->saveCsv($fields);

        exit;
    }

    /**
     * @param $fields
     */
    public function saveCsv($fields)
    {
        $fileName   = 'domain_date ('.date('Y-m-d').').csv';
        $filePath   = $fileName;
        $fp = fopen($filePath, 'w');
        foreach ($fields as $field) {
            fputcsv($fp, $field);
        }
        fclose($fp);

        echo "CSV file formed \n";
    }
}
(new MaracasTest($source))->init();