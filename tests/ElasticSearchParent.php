<?php // vim:set ts=4 sw=4 et:
require_once 'helper.php';
/**
 * These tests cover the union of every transports api
 */
abstract class ElasticSearchParent extends PHPUnit_Framework_TestCase {
    
    protected $search = null;

    protected function generateDocument($words, $len=4) {
        $sentence = "";
        while ($len > 0) {
            shuffle($words);
            $sentence .= $words[0] . " ";
            $len--;
        }
        return array('title' => $sentence);
    }
    protected function addDocuments($indexes=array("test-index"), $num=3, $rand=false) {
        $words = array("cool", "dog", "lorem", "ipsum", "dolor", "sit", "amet");
        // Generate documents
        foreach ($indexes as $ind) {
            $this->search->setIndex($ind);
            $tmpNum = $num;

            // Index documents
            while ($tmpNum > 0) {
                $tmpNum--;
                if ($rand)
                    $doc = $this->generateDocument($words, 5);
                else
                    $doc = array('title' => 'One cool document');
                $this->search->index($doc, $tmpNum + 1);
            }
        }
    }

    /**
     * Test indexing a new document
     */
    public function testIndexingDocument() {
        $doc = array(
            'title' => 'One cool document',
            'tag' => 'cool'
        );
        $resp = $this->search->index($doc, 1);

        $this->assertTrue($resp['ok'] == 1);
    }
    
    /**
     * Test indexing a new document and having an auto id
     * This means dupes will occur
     */
    public function testStringSearch() {
        $this->addDocuments(array("test-index"), 2);
        sleep(1); // Indexing is only near real time
        $hits = $this->search->search("title:cool");
        $this->assertEquals(2, $hits['hits']['total']);
    }
}