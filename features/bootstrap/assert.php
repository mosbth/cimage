<?php
/**
 *  Assert functions.
 */
function assertNotEquals($expected, $actual)
{
    if (!($expected !== $actual)) {
        throw new Exception("Failed asserting that '$expected' is not equal to '$actual'.");
    }
};



function assertEquals($expected, $actual)
{
    if (!($expected === $actual)) {
        throw new Exception("Failed asserting that '$expected' is equal to '$actual'.");
    }
};



/**
 * Check that $needle is an element of $haystack.
 */

function assertContains($needle, $haystack)
{
    if (!in_array($needle, $haystack)) {
        throw new Exception("Failed asserting that '$needle' is not in haystack.");
    }
}
