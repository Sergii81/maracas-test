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
                    $fields['product_name'][] = strip_tags($product->productHeadline);
                    if($product->introtext) {
                        $fields['amazon_introtext'][] = strip_tags($product->introtext);
                    }
                    if($product->award) {
                        $fields['amazon_award'][] = strip_tags($product->award);
                    }
                    if($product->productSummary) {
                        $fields['amazon_product_summary'][] = strip_tags($product->productSummary);
                    }
                }
            }
        }

        if(!empty($fields['amazon_introtext'])) {
            $fields['amazon_introtext_count'][] = count($fields['amazon_introtext']);
        } else {
            $fields['amazon_introtext_count'][] = 'Fields introtext not found';
        }

        if(!empty($fields['amazon_award'])) {
            $fields['amazon_award_count'][] = count($fields['amazon_award']);
        } else {
            $fields['amazon_award_count'][] = 'Fields award not found';
        }

        if(!empty($fields['amazon_product_summary'])) {
            $fields['amazon_product_summary_count'][] = count($fields['amazon_product_summary']);
        } else {
            $fields['amazon_product_summary_count'][] = 'Fields productSummary not found';
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