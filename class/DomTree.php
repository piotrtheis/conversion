<?php
/**
 * DomTree
 *
 * Dump DomDocument based documents, suiting debugging needs
 *
 * @author hakre <http://hakre.wordpress.com/>
 * @link   http://stackoverflow.com/questions/26321597/getting-price-from-amazon-with-xpath/26323824#26323824
 * @link   http://stackoverflow.com/questions/12108324/how-to-get-a-raw-from-a-domnodelist/12108732#12108732
 * @link   http://stackoverflow.com/questions/684227/debug-a-domdocument-object-in-php/8631974#8631974
 */
 
/**
 * Decorator Stub class for a RecursiveIterator
 */
abstract class DomTree_RecursiveIteratorDecoratorStub extends IteratorIterator implements RecursiveIterator
{
    public function __construct(RecursiveIterator $iterator)
    {
        parent::__construct($iterator);
    }
 
    public function hasChildren()
    {
        return $this->getInnerIterator()->hasChildren();
    }
 
    public function getChildren()
    {
        return new static($this->getInnerIterator()->getChildren());
    }
}
 
class DomTree_NodesArrayIterator implements Iterator
{
    /**
     * @var array
     */
    private $nodes;
 
    private $virtual;
 
    /**
     * @param array $DOMNodes
     */
    public function __construct(array $DOMNodes)
    {
        $this->nodes = $DOMNodes;
    }
 
    /**
     * @return DOMNode
     */
    public function current()
    {
        $keys = array_keys($this->nodes);
        return $this->nodes[$keys[$this->virtual]];
    }
 
    /**
     * Move forward to next element
     *
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->virtual++;
    }
 
    /**
     * Return the key of the current element
     *
     * @link http://php.net/manual/en/iterator.key.php
     * @return int|string scalar on success, integer 0 on failure.
     */
    public function key()
    {
        $this->virtual;
    }
 
    /**
     * Checks if current position is valid
     *
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated. Returns true on success or false on failure.
     */
    public function valid()
    {
        return $this->virtual < count($this->nodes);
    }
 
    /**
     * Rewind the Iterator to the first element
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->virtual = 0;
    }
}
 
/**
 * Iterator for DOMNode(s)
 */
class DomTree_DOMIterator extends IteratorIterator
{
    /**
     * @param array|DOMNode|DOMNodeList $nodeOrNodes
     *
     * @throws InvalidArgumentException
     */
    public function __construct($nodeOrNodes)
    {
        if ($nodeOrNodes instanceof DOMNode) {
            $nodeOrNodes = array($nodeOrNodes);
        } elseif ($nodeOrNodes instanceof DOMNodeList) {
            $nodeOrNodes = new IteratorIterator($nodeOrNodes);
        }
        if (is_array($nodeOrNodes)) {
            $nodeOrNodes = new ArrayIterator($nodeOrNodes);
        }
 
        if (!$nodeOrNodes instanceof Iterator) {
            throw new InvalidArgumentException('Not an array, DOMNode or DOMNodeList given.');
        }
 
        parent::__construct($nodeOrNodes);
    }
}
 
/**
 * Recursive Iterator for DOMNode(s)
 */
class DomTree_DOMRecursiveIterator extends DomTree_DOMIterator implements RecursiveIterator
{
    public function hasChildren()
    {
        /* @var $current DOMNode */
        $current = $this->current();
        return $current->hasChildNodes();
    }
 
    public function getChildren()
    {
        /* @var $current DOMNode */
        $current = $this->current();
        return new self($current->childNodes);
    }
}
 
 
class DomTree_DOMRecursiveDecoratorStringAsCurrent extends DomTree_RecursiveIteratorDecoratorStub
{
    public function current()
    {
        /* @var $node DOMNode */
        $node     = parent::current();
        $nodeType = new DomTree_DOMNodeType($node);
 
        switch ($nodeType->getType()) {
            case XML_ELEMENT_NODE:
                return $this->tag($node);
 
            case XML_TEXT_NODE:
                return $this->string($node->nodeValue);
 
            case XML_DOCUMENT_NODE;
                return $this->docnode($node);
 
            case XML_COMMENT_NODE:
                return $this->comment($node);
 
            default:
                return sprintf('[%s (%d)]', $nodeType, $nodeType->getType());
        }
    }
 
    private function comment($node)
    {
        return '<!-- ' . $this->string($node->nodeValue);
    }
 
    private function string($string)
    {
        $string = strtr(
            $string,
            array("\xEF\xBB\xBF" => '/!\ BOM:UTF-8 /!\\', "\0" => '\0', "\n" => '\n', "\t" => '\t', "\r" => '\r')
        );
        if (strlen($string) > 80) {
            $string = substr($string, 0, 77) . '...';
        }
        return sprintf('"%s"', $string);
    }
 
    /**
     * @param DOMDocument $node
     *
     * @return string
     */
    private function docnode(DOMDocument $node)
    {
        var_dump($node);
        $tag = "<{$node->documentURI}>";
        return $tag;
    }
 
    /**
     * @param DOMElement $node
     * @param array      $attributes to optionally expand to
     *
     * @return string
     */
    private function tag(DOMElement $node, $attributes = array('id', 'class', 'href'))
    {
        $tag = "<$node->tagName";
        if ($node->hasAttributes()) {
            foreach ($attributes as $attribute) {
                if ($att = $node->getAttribute($attribute)) {
                    $tag .= " " . $attribute . "=" . $this->string($att);
                }
            }
        }
        return "$tag>";
 
    }
}
 
class DomTree_DOMNodeType
{
    private $type;
 
    public function __construct($typeOrNode)
    {
        if ($typeOrNode instanceof DOMNode) {
            $typeOrNode = $typeOrNode->nodeType;
        }
 
        if (!is_int($typeOrNode)) {
            $typeOrNode = 0;
        }
 
        $this->type = $typeOrNode;
    }
 
    public function __toString()
    {
        return $this->getString();
    }
 
    public function getType()
    {
        return $this->type;
    }
 
    public function getString()
    {
        return $this->nodeTypeText($this->type);
    }
 
    private function nodeTypeText($nodeType)
    {
        $constants = array_flip($this->getDOMConstants('^XML_.+_NODE$'));
        if (isset($constants[$nodeType])) {
            $text = $constants[$nodeType];
        } else {
            $text = 'XML_UNKNOWN_NODE';
        }
 
        return $text;
    }
 
    private function getDOMConstants($filter = null)
    {
        $constants = get_defined_constants(true);
        $constants = $constants['dom'];
        if ($filter) {
            $pattern = sprintf('/%s/', $filter);
            foreach ($constants as $key => $value) {
                if (!preg_match($pattern, $key)) {
                    unset($constants[$key]);
                }
            }
        }
        return $constants;
    }
}
 
class DomTree
{
    /**
     * @static
     *
     * @param array|DOMNode|DOMNodeList $nodeOrNodes
     * @param int                       $maxDepth (optional)
     */
    public static function dump($nodeOrNodes, $maxDepth = 0)
    {
        $iterator  = new DomTree_DOMRecursiveIterator($nodeOrNodes);
        $decorated = new DomTree_DOMRecursiveDecoratorStringAsCurrent($iterator);
        $tree      = new RecursiveTreeIterator($decorated);
        $tree->setPrefixPart(RecursiveTreeIterator::PREFIX_END_LAST, '`');
        $tree->setPrefixPart(RecursiveTreeIterator::PREFIX_END_HAS_NEXT, '+');
        $maxDepth && $tree->setMaxDepth($maxDepth);
        foreach ($tree as $key => $value) {
            echo $value . "\n";
        }
    }
 
    /**
     * @static
     *
     * @param DOMNode $node
     * @param int     $maxDepth (optional)
     *
     * @return string
     */
    public static function asString(DOMNode $node, $maxDepth = 0)
    {
        ob_start();
        self::dump($node, $maxDepth);
        return ob_get_clean();
    }
}