<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Weblink Table class
 *
 * @package		Joomla.Administrator
 * @subpackage	Weblinks
 * @since		1.5
 */
class TableWeblink extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;

	/**
	 * @var int
	 */
	var $catid = null;

	/**
	 * @var int
	 */
	var $sid = null;

	/**
	 * @var string
	 */
	var $title = null;

	/**
	 * @var string
	 */
	var $alias = null;

	/**
	 * @var string
	 */
	var $url = null;

	/**
	 * @var string
	 */
	var $description = null;

	/**
	 * @var datetime
	 */
	var $date = null;

	/**
	 * @var int
	 */
	var $hits = null;

	/**
	 * @var int
	 */
	var $state = null;

	/**
	 * @var boolean
	 */
	var $checked_out = 0;

	/**
	 * @var time
	 */
	var $checked_out_time = 0;

	/**
	 * @var int
	 */
	var $ordering = null;

	/**
	 * @var int
	 */
	var $archived = null;

	/**
	 * @var int
	 */
	var $approved = null;

	/**
	 * @var string
	 */
	var $params = null;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	function __construct(& $db) {
		parent::__construct('#__weblinks', 'id', $db);
	}

	/**
	 * Overloaded bind function
	 *
	 * @acces public
	 * @param array $hash named array
	 * @return null|string	null is operation was satisfactory, otherwise returns an error
	 * @see JTable:bind
	 * @since 1.5
	 */
	function bind($array, $ignore = '')
	{
		if (key_exists('params', $array) && is_array($array['params']))
		{
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 * @since 1.0
	 */
	function check()
	{
		if (JFilterInput::checkAttribute(array ('href', $this->url))) {
			$this->setError(JText::_('Please provide a valid URL'));
			return false;
		}

		/** check for valid name */
		if (trim($this->title) == '') {
			$this->setError(JText::_('Your Weblink must contain a title.'));
			return false;
		}

		if (!(eregi('http://', $this->url) || (eregi('https://', $this->url)) || (eregi('ftp://', $this->url)))) {
			$this->url = 'http://'.$this->url;
		}

		/** check for existing name */
		$query = 'SELECT id FROM #__weblinks WHERE title = '.$this->_db->Quote($this->title).' AND catid = '.(int) $this->catid;
		$this->_db->setQuery($query);

		$xid = intval($this->_db->loadResult());
		if ($xid && $xid != intval($this->id)) {
			$this->setError(JText::sprintf('WARNNAMETRYAGAIN', JText::_('Web Link')));
			return false;
		}

		if (empty($this->alias)) {
			$this->alias = $this->title;
		}
		$this->alias = JFilterOutput::stringURLSafe($this->alias);
		if (trim(str_replace('-','',$this->alias)) == '') {
			$datenow =& JFactory::getDate();
			$this->alias = $datenow->toFormat("%Y-%m-%d-%H-%M-%S");
		}

		return true;
	}
}
