<?php
/**
 * CLinkPager class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * CLinkPager displays a list of hyperlinks that lead to different pages of target.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id$
 * @package system.web.widgets.pagers
 * @since 1.0
 */
class CLinkPager extends CBasePager
{
	const CSS_FIRST_PAGE='first';
	const CSS_LAST_PAGE='last';
	const CSS_PREVIOUS_PAGE='previous';
	const CSS_NEXT_PAGE='next';
	const CSS_INTERNAL_PAGE='page';
	const CSS_HIDDEN_PAGE='hidden';
	const CSS_SELECTED_PAGE='selected';
	const BOOKS = 10;
    const SHEETMUSIC = 15;
    const AUDIO = 20;
    const MUSIC = 22;
    const PERIODIC = 30;
    const VIDEO = 40;
    const SOFT = 24;
    const MAPS = 60;
    const PRINTED = 50;
	/**
	 * @var string the CSS class for the first page button. Defaults to 'first'.
	 * @since 1.1.11
	 */
	public $firstPageCssClass=self::CSS_FIRST_PAGE;
	/**
	 * @var string the CSS class for the last page button. Defaults to 'last'.
	 * @since 1.1.11
	 */
	public $lastPageCssClass=self::CSS_LAST_PAGE;
	/**
	 * @var string the CSS class for the previous page button. Defaults to 'previous'.
	 * @since 1.1.11
	 */
	public $previousPageCssClass=self::CSS_PREVIOUS_PAGE;
	/**
	 * @var string the CSS class for the next page button. Defaults to 'next'.
	 * @since 1.1.11
	 */
	public $nextPageCssClass=self::CSS_NEXT_PAGE;
	/**
	 * @var string the CSS class for the internal page buttons. Defaults to 'page'.
	 * @since 1.1.11
	 */
	public $internalPageCssClass=self::CSS_INTERNAL_PAGE;
	/**
	 * @var string the CSS class for the hidden page buttons. Defaults to 'hidden'.
	 * @since 1.1.11
	 */
	public $hiddenPageCssClass=self::CSS_HIDDEN_PAGE;
	/**
	 * @var string the CSS class for the selected page buttons. Defaults to 'selected'.
	 * @since 1.1.11
	 */
	public $selectedPageCssClass=self::CSS_SELECTED_PAGE;
	/**
	 * @var integer maximum number of page buttons that can be displayed. Defaults to 10.
	 */
	public $maxButtonCount=4;
	/**
	 * @var string the text label for the next page button. Defaults to 'Next &gt;'.
	 */
	public $nextPageLabel;
	/**
	 * @var string the text label for the previous page button. Defaults to '&lt; Previous'.
	 */
	public $prevPageLabel;
	/**
	 * @var string the text label for the first page button. Defaults to '&lt;&lt; First'.
	 */
	public $firstPageLabel;
	/**
	 * @var string the text label for the last page button. Defaults to 'Last &gt;&gt;'.
	 */
	public $lastPageLabel;
	/**
	 * @var string the text shown before page buttons. Defaults to 'Go to page: '.
	 */
	public $header;
	/**
	 * @var string the text shown after page buttons.
	 */
	public $footer='';
	/**
	 * @var mixed the CSS file used for the widget. Defaults to null, meaning
	 * using the default CSS file included together with the widget.
	 * If false, no CSS file will be used. Otherwise, the specified CSS file
	 * will be included when using this widget.
	 */
	public $cssFile;
	/**
	 * @var array HTML attributes for the pager container tag.
	 */
	public $htmlOptions=array();

	/**
	 * Initializes the pager by setting some default property values.
	 */
	public function init()
	{
		if($this->nextPageLabel===null)
			$this->nextPageLabel=Yii::t('yii','Next &gt;');
		if($this->prevPageLabel===null)
			$this->prevPageLabel=Yii::t('yii','&lt; Previous');
		if($this->firstPageLabel===null)
			$this->firstPageLabel=Yii::t('yii','&lt;&lt; First');
		if($this->lastPageLabel===null)
			$this->lastPageLabel=Yii::t('yii','Last &gt;&gt;');
		if($this->header===null)
			$this->header=Yii::t('yii','Go to page: ');

		if(!isset($this->htmlOptions['id']))
			$this->htmlOptions['id']=$this->getId();
		if(!isset($this->htmlOptions['class']))
			$this->htmlOptions['class']='yiiPager';
		
	}

	/**
	 * Executes the widget.
	 * This overrides the parent implementation by displaying the generated page buttons.
	 */
	public function run()
	{
		$this->registerClientScript();
		$buttons=$this->createPageButtons();
		if(empty($buttons))
			return;
		echo $this->header;
		unset($this->htmlOptions['char']);
		echo CHtml::tag('ul',$this->htmlOptions,implode("\n",$buttons));
		echo $this->footer;
	}

	/**
	 * Creates the page buttons.
	 * @return array a list of page buttons (in HTML code).
	 */
	protected function createPageButtons()
	{
		if(($pageCount=$this->getPageCount())<=1)
			return array();

		list($beginPage,$endPage)=$this->getPageRange();
		$currentPage=$this->getCurrentPage(false); // currentPage is calculated in getPageRange()
		$buttons=array();

		// first page
		
		if ($this->firstPageLabel) {
		
			$buttons[]=$this->createPageButton($this->firstPageLabel,1,$this->firstPageCssClass,$currentPage<=0,false);
		
		}
		// prev page
		
		
		
		$currentPage = (isset($_GET['page'])) ? (int) $_GET['page'] : 1;
		
		if ($currentPage == 0) { $currentPage = 1; }
		
		$page = $currentPage;
		
		if( $page <=0 ) 
			$page=1;
		
		
		
		if ($currentPage>1 AND $currentPage>0) {
		
			$buttons[]=$this->createPageButton($this->prevPageLabel,$page-1,$this->previousPageCssClass,$currentPage<=0,false);
		
		}
		//var_dump($beginPage);
		
		// internal pages
		
		
		for($i=$beginPage;$i<=$endPage;++$i){
		//if (($i-1) < 0) { $j = 1; }	else { $j = ($i-1); }
			
			$buttons[]=$this->createPageButton($i+1,$i+1,$this->internalPageCssClass,false, ($i)==$currentPage-1);
			
			//var_dump($i . '=' . $currentPage);
			
		}
		// next page
		if($page>=$pageCount-1)
			$page=$pageCount-1;
		
		
		$buttons[]=$this->createPageButton($this->nextPageLabel,$page+1,$this->nextPageCssClass,$currentPage>=$pageCount-1,false);

		// last page
		if ($this->lastPageLabel) {
			$buttons[]=$this->createPageButton($this->lastPageLabel,$pageCount-1,$this->lastPageCssClass,$currentPage>=$pageCount-1,false);
		}
		return $buttons;
	}

	/**
	 * Creates a page button.
	 * You may override this method to customize the page buttons.
	 * @param string $label the text label for the button
	 * @param integer $page the page number
	 * @param string $class the CSS class for the page button.
	 * @param boolean $hidden whether this page button is visible
	 * @param boolean $selected whether this page button is selected
	 * @return string the generated button
	 */
	protected function createPageButton($label,$page,$class,$hidden,$selected)
	{
		if($hidden || $selected)
			$class.=' '.($hidden ? $this->hiddenPageCssClass : $this->selectedPageCssClass);
		
		//var_dump($this->createPageUrl($page));
		
		
		
		if ((int) $_REQUEST['entity'] == 0) {
			
			$page_url = trim($_SERVER['REQUEST_URI'],'/');
			
		} else {
			
			//$page_url = Entity::GetUrlKey[(int) $_REQUEST['entity']];
			
			switch ((int) $_REQUEST['entity']) {
				case self::PERIODIC : $page_url =  'periodics'; break;
				case self::BOOKS : $page_url =  'books'; break;
				case self::SHEETMUSIC : $page_url =  'sheetmusic'; break;
				case self::AUDIO : $page_url =  'audio'; break;
				case self::MUSIC : $page_url =  'music'; break;
				case self::VIDEO : $page_url =  'video'; break;
				case self::SOFT : $page_url =  'soft'; break;
				case self::MAPS : $page_url =  'maps'; break;
				case self::PRINTED : $page_url =  'printed'; break;
				default: throw new CException('Wrong entity [' . $entity . ']');
			}
			
		}
		
		
		
		$url = '/'.$page_url.'?page='.$page;
		
		return '<li class="'.$class.'">'.CHtml::link($label,$url).'</li>';
	}

	/**
	 * @return array the begin and end pages that need to be displayed.
	 */
	protected function getPageRange()
	{
		$currentPage=$this->getCurrentPage();
		$pageCount=$this->getPageCount();

		$beginPage=max(0, $currentPage-(int)($this->maxButtonCount/2));
		if(($endPage=$beginPage+$this->maxButtonCount-1)>=$pageCount)
		{
			$endPage=$pageCount-1;
			$beginPage=max(0,$endPage-$this->maxButtonCount+1);
		}
		return array($beginPage,$endPage);
	}

	/**
	 * Registers the needed client scripts (mainly CSS file).
	 */
	public function registerClientScript()
	{
		if($this->cssFile!==false)
			self::registerCssFile($this->cssFile);
	}

	/**
	 * Registers the needed CSS file.
	 * @param string $url the CSS URL. If null, a default CSS URL will be used.
	 */
	public static function registerCssFile($url=null)
	{
		if($url===null)
			$url=CHtml::asset(Yii::getPathOfAlias('system.web.widgets.pagers.pager').'.css');
		Yii::app()->getClientScript()->registerCssFile($url);
	}
}
