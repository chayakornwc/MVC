<?php
namespace Core;

/**
 * Return snippets of HTML with variable replacement.
 *
 * @copyright   2012 Christopher Hill <cjhill@gmail.com>
 * @author      Christopher Hill <cjhill@gmail.com>
 * @since       15/09/2012
 */
class Snippet extends Cache
{
	/**
	 * The variables that we wish to replace.
	 *
	 * <code>
	 * array(
	 *     'foo'    => 'bar',
	 *     'foobar' => 'The replacement string'
	 * )
	 * </code>
	 *
	 * @access private
	 * @var    array
	 */
	private $_variable = array();

	/**
	 * Start to build the snippet.
	 *
	 * We only want to pass in a snippet file at the moment. We do not want to give the
	 * constructor too much power, and we would rather build the snippet up as we go along.
	 *
	 * @access public
	 * @param  string    $file The snippet we wish to include.
	 * @param  string    $path The path to the snippet we wish to include.
	 * @throws Exception       If the snippet cannot be located.
	 */
	public function __construct($file, $path) {
		parent::__construct($file, $path);
	}

	/**
	 * Add a variable to be replaced.
	 *
	 * Note: If you pass in the same variable twice then it will overwrite the first.
	 *
	 * @access public
	 * @param  string  $variable The variable we wish to add.
	 * @param  string  $value    The value of the variable.
	 * @return Snippet
	 */
	public function addVariable($variable, $value) {
		$this->_variable[$variable] = $value;
		return $this;
	}

	/**
	 * Return the snippet with the variables replaced.
	 *
	 * If we can use a cached version of the file then we will, otherwise we
	 * will render the snippet fresh.
	 *
	 * @access public
	 * @return string
	 */
	public function render() {
		// Can we use a cached snippet?
		if ($this->cachedFileAvailable()) {
			// We can use a cached copy, much quick
			return $this->getCachedFile();
		}

		// Start object buffering
		ob_start();

		// Extract variables
		extract($this->_variable);

		// Include the snippet
		include Config::get('path', 'view_snippet') . $this->_file;

		// Place the buffer contents into a string
		$content = ob_get_contents();
		ob_end_clean();

		// Do we want to save this to the cache
		if ($this->_enableCache) {
			$this->saveFileToCache($content);	
		}

		// Rendering complete
		return $content;
	}
}