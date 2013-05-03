<?php

if(class_exists('PHPUnit_Framework_Constraint')){
/**
 * @codeCoverageIgnore
 */
class PHPUnit_Framework_Constraint_ExceptionCode extends \PHPUnit_Framework_Constraint
{
    /**
     * Evaluates the constraint for parameter $other. Returns TRUE if the
     * constraint is met, FALSE otherwise.
     *
     * @param  Exception $other
     * @return boolean
     */
    protected function matches($other)
    {
        if (is_numeric($this->expectedCode)) {
            return (string) $other->getCode() == (string) $this->expectedCode;
        }
        if (method_exists($other, 'getMessageCode')) {
            return $this->expectedCode==$other->getMessageCode();
        } else {
            return false;
        }

    }

    /**
     * @var integer
     */
    protected $expectedCode;

    /**
     * @param integer $expected
     */
    public function __construct($expected)
    {
        $this->expectedCode = $expected;
    }

    /**
     * Returns the description of the failure
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * @param  mixed  $other Evaluated value or object.
     * @return string
     */
    protected function failureDescription($other)
    {
	$code = method_exists($other, 'getMessageCode') ? $other->getMessageCode():$other->getCode();
        return sprintf(
            '%s is equal to expected exception code %s',
            PHPUnit_Util_Type::export($code), PHPUnit_Util_Type::export($this->expectedCode)
        );
    }

    /**
     * @return string
     */
    public function toString()
    {
        return 'exception code is ';
    }
}
}