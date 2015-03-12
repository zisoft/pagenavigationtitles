<?php
/**
* @version		1.7
* @copyright	Copyright (C) 2011,2013 Mario Zimmermann. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

class plgContentPageNavigationTitles extends JPlugin
{

    public function onContentBeforeDisplay($context, &$row, &$params, $page=0)
	{
		$view  = JRequest::getCmd('view');
		$print = JRequest::getBool('print');

		if ($print) {
			return false;
		}

		if (($context == 'com_content.article') && ($view == 'article') && $params->get('show_item_navigation'))
		{
			$html     = '';
			$db       = JFactory::getDbo();
			$user     = JFactory::getUser();
			$app      = JFactory::getApplication();
			$lang     = JFactory::getLanguage();
			$nullDate = $db->getNullDate();

			$date   = JFactory::getDate();
			$config = JFactory::getConfig();
			$now    = $date->toSql();

			$uid        = $row->id;
			$option     = 'com_content';
			$canPublish = $user->authorise('core.edit.state', $option.'.article.'.$row->id);

			// The following is needed as different menu items types utilise a different param to control ordering.
			// For Blogs the `orderby_sec` param is the order controlling param.
			// For Table and List views it is the `orderby` param.
			$params_list = $params->toArray();
			if (array_key_exists('orderby_sec', $params_list)) {
				$order_method = $params->get('orderby_sec', '');
			} else {
				$order_method = $params->get('orderby', '');
			}
			// Additional check for invalid sort ordering.
			if ($order_method == 'front') {
				$order_method = '';
			}

			// Determine sort order.
			switch ($order_method) {
				case 'date' :
					$orderby = 'a.created';
					break;
				case 'rdate' :
					$orderby = 'a.created DESC';
					break;
				case 'alpha' :
					$orderby = 'a.title';
					break;
				case 'ralpha' :
					$orderby = 'a.title DESC';
					break;
				case 'hits' :
					$orderby = 'a.hits';
					break;
				case 'rhits' :
					$orderby = 'a.hits DESC';
					break;
				case 'order' :
					$orderby = 'a.ordering';
					break;
				case 'author' :
					$orderby = 'a.created_by_alias, u.name';
					break;
				case 'rauthor' :
					$orderby = 'a.created_by_alias DESC, u.name DESC';
					break;
				case 'front' :
					$orderby = 'f.ordering';
					break;
				default :
					$orderby = 'a.ordering';
					break;
			}

			$xwhere = ' AND (a.state = 1 OR a.state = -1)' .
			' AND (publish_up = '.$db->Quote($nullDate).' OR publish_up <= '.$db->Quote($now).')' .
			' AND (publish_down = '.$db->Quote($nullDate).' OR publish_down >= '.$db->Quote($now).')';

			// Array of articles in same category correctly ordered.
			$query	= $db->getQuery(true);
			$query->select('a.id, a.title, '
					.'CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug, '
					.'CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug');
			$query->from('#__content AS a');
			$query->leftJoin('#__categories AS cc ON cc.id = a.catid');
			$query->where('a.catid = '. (int)$row->catid .' AND a.state = '. (int)$row->state
						. ($canPublish ? '' : ' AND a.access = ' .(int)$row->access) . $xwhere);
			$query->order($orderby);
			if ($app->isSite() && $app->getLanguageFilter()) {
				$query->where('a.language in ('.$db->quote($lang->getTag()).','.$db->quote('*').')');
			}

			$db->setQuery($query);
			$list = $db->loadObjectList('id');

			// This check needed if incorrect Itemid is given resulting in an incorrect result.
			if (!is_array($list)) {
				$list = array();
			}

			reset($list);

			// location of current content item in array list
			$location = array_search($uid, array_keys($list));

			$rows = array_values($list);
            
			$row->prev  = null;
			$row->next  = null;
			$prev_title = null;
			$next_title = null;

			if ($location -1 >= 0) 	{
				// the previous content item cannot be in the array position -1
				$row->prev = $rows[$location -1];
			}

			if (($location +1) < count($rows)) {
				// the next content item cannot be in an array position greater than the number of array postions
				$row->next = $rows[$location +1];
			}


			$pnSpace = "";
			if (JText::_('JGLOBAL_LT') || JText::_('JGLOBAL_GT')) {
				$pnSpace = " ";
			}

			if ($row->prev) {
				$prev_title = $row->prev->title;
				$row->prev = JRoute::_(ContentHelperRoute::getArticleRoute($row->prev->slug, $row->prev->catslug));
			} else {
				$row->prev = '';
			}

			if ($row->next) {
				$next_title = $row->next->title;
				$row->next = JRoute::_(ContentHelperRoute::getArticleRoute($row->next->slug, $row->next->catslug));
			} else {
				$row->next = '';
			}
			
			
			// Get the plugin parameters
			$position           = $this->params->get('position', 1);
			$text_arrows        = $this->params->get('text_arrows', 1);
			$pre_text_prev      = $this->params->get('pre_text_prev', '');
			$pre_text_next      = $this->params->get('pre_text_next', '');
			$base_class         = $this->params->get('baseclass');
			$prev_class         = $this->params->get('prevclass');
			$next_class         = $this->params->get('nextclass');
			$pretext_prev_class = $this->params->get('pretextprevclass');
			$pretext_next_class = $this->params->get('pretextnextclass');

			$text_arrow_prev = $this->params->get('arrow_text_prev');
			$text_arrow_next = $this->params->get('arrow_text_next');
			$arrowtext_prev_class = $this->params->get('arrowtextprevclass');
			$arrowtext_next_class = $this->params->get('arrowtextnextclass');

			$title_class = $this->params->get('titleclass');

			$text_arrow_position = $this->params->get('text_arrow_position', 1);



			$arrow_left  = '';
			$arrow_right = '';
			
			if ($text_arrows)
			{
				if ($text_arrow_prev) {
					$arrow_left = $text_arrow_prev ;
				} else {
					$arrow_left = JText::_('JGLOBAL_LT');
				}
				if ($text_arrow_next) {
					$arrow_right = $text_arrow_next;
				} else {
					$arrow_right = JText::_('JGLOBAL_GT');
				}

				
			}
            
			// output
			if ($row->prev || $row->next)
			{			
				if ($row->prev)
				{

					$arrow = $this->wrap_text('span', $arrowtext_prev_class, $arrow_left); //arrow

					$prev_markup = '<a href="'. $row->prev .'" rel="next">';
					if ($text_arrow_position == 1 || $text_arrow_position == 3) {
						$prev_markup .= $arrow;
					}

					if ($pre_text_prev)
					{
						$prev_markup .= $this->wrap_text('span', $pretext_prev_class, $pre_text_prev);
					}
					$prev_markup .= $this->wrap_text('span', $title_class, $prev_title);
					if ($text_arrow_position == 2 || $text_arrow_position == 4) {
						$prev_markup .= $arrow;
					}
					$prev_markup .= '</a>';
					$html .= $this->wrap_text('li', $prev_class, $prev_markup);
				}

				if ($row->next)
				{
					$arrow = $this->wrap_text('span', $arrowtext_next_class, $arrow_right); 


					$next_markup = '<a href="'. $row->next .'" rel="next">';

					if ($text_arrow_position == 2 || $text_arrow_position == 3) {
						$next_markup .= $arrow;
					}


					if ($pre_text_next)
					{
						$next_markup .= $this->wrap_text('span', $pretext_next_class, $pre_text_next);
					}
					$next_markup .= $this->wrap_text('span', $title_class, $next_title);
					if ($text_arrow_position == 1 || $text_arrow_position == 4) {
						$next_markup .= $arrow;
					}
										
					$next_markup .= '</a>';
					$html .= $this->wrap_text('li', $next_class, $next_markup);
				}

				$html = $this->wrap_text('ul', $base_class, $html);
				
				if ($position == 0 || $position == 2) {
					// display before content
					$row->text = $html . $row->text;
				}
				
				if ($position == 1 || $position == 2) {
					// display after content
					$row->text .= $html;
				}
			}
		}
	}

	public function wrap_text($tagname = 'div', $class='', $content=''){
		$wrap = '<' . $tagname;
		if (!empty($class)) {
			$wrap .= ' class="'. $class . '"';
		}
		$wrap .= '>' .$content . '</'.$tagname.'>';
		return $wrap;
	}

}
