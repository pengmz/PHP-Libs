<?php

class Pager {
	
	public static $pagination;
	
	public static function init($items = array(), $per_page_items = 20) {
		$items_for_current_page = array();

		if ($items){			
			$pagination = self::paging(count($items), $per_page_items);		
			$begin_index = $pagination->getBeginIndex();
			$end_index = $pagination->getEndIndex();
			$items_for_current_page = array_slice($items, $begin_index, $end_index - $begin_index);
		}
		
		return $items_for_current_page;
	}
	
	public static function paging($total_items = 200, $per_page_items = 20) {			
		$curr_page = $_GET['page_num'];		
		self::$pagination = new Pagination($total_items, $per_page_items, $curr_page);
		return self::$pagination;		
	}
	
	public static function links($url = null) {
		if (is_null($url)) {
			$url = $_SERVER['PHP_SELF'];
		}
		$url .= (stripos($url, '?') !== false) ? '&' : '?';	
		
		$pagination = self::$pagination;
		if ($pagination){
			$page_count = $pagination->getPageCount();
			$curr_page = $pagination->getCurrentPage();
			$links_on_page = 6;
			
			if (!$pagination->isOnePage()){							
				
				if (! $pagination->isFirstPage()) {
					$links .= self::generate_link($url, $pagination->getFirstPage(), 'First');
					$links .= self::generate_link($url, $pagination->getPrevPage(), 'Prev');
				}
				
				$minPage = max($curr_page - $links_on_page/2, 1);
				$maxPage = min($minPage + $links_on_page, $page_count);
				
				for($i = $minPage; $i <= $maxPage; $i ++) {
					if ($i != $curr_page){
						$links .= self::generate_link($url, $i, $i);
					} else {
						$links .= self::generate_link($url, $i, $i, TRUE);
					}
				}
				if (! $pagination->isLastPage()) {
					$links .= self::generate_link($url, $pagination->getNextPage(), 'Next');
					$links .= self::generate_link($url, $pagination->getLastPage(), 'Last');
				}
				
			}
			
			echo $links;
		}
	}
	
	private static function generate_link($url, $page_num, $link_text, $is_current_page = FALSE) {
		$param = $_GET;
		$param['page_num'] = $page_num;
		$url .= http_build_query($param);	

		if ($is_current_page){
			return '<li class="active"><a href="' . $url . '">' . $link_text . '</a></li>';	
		} else {
			return '<li><a href="' . $url . '">' . $link_text . '</a></li>';
		}
	}

	private static function generate_per_page_select() {
        $str = 'Per Page:
        <select id="per_page" style="font-family: arial;">
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
            <option value="10000">Show All</option>
        </select>';
        return $str;
	}
}

class Pagination {
	
	private $total_items;
	
	private $per_page_items;
	
	private $page_count;
	
	private $curr_page;
	
	public function Pagination($total_items = 200, $per_page_items = 20, $curr_page = 1) {
		if (is_null($curr_page)){
			$curr_page = 1;
		}
		$this->total_items = $total_items;
		$this->per_page_items = $per_page_items;
		$this->page_count = ceil($total_items / $per_page_items);
		$this->curr_page = $curr_page;
	}
	
	public function getPageCount() {
		return $this->page_count;
	}
	
	public function getCurrentPage() {
		return $this->curr_page;
	}
	
	public function getFirstPage() {
		return 1;
	}
	
	public function getPrevPage() {
		if (! $this->isFirstPage()) {
			return $this->curr_page - 1;
		} else {
			return $this->curr_page;
		}
	}
	
	public function getNextPage() {
		if (! $this->isLastPage()) {
			return $this->curr_page + 1;
		} else {
			return $this->curr_page;
		}
	}
	
	public function getLastPage() {
		return $this->page_count;
	}
	
	public function isFirstPage() {
		return $this->curr_page <= $this->getFirstPage();
	}
	
	public function isMiddlePage() {
		return ! ($this->isFirstPage() || $this->isLastPage());
	}
	
	public function isLastPage() {
		return $this->curr_page >= $this->getLastPage();
	}
	
	public function isOnePage() {
		return $this->page_count == 1;
	}
	
	public function hasNextPage() {
		return ! $this->isLastPage();
	}
	
	public function hasPreviousPage() {
		return ! $this->isFirstPage();
	}
	
	public function getBeginIndex() {
		return ($this->curr_page - 1) * $this->per_page_items;
	}
	
	public function getEndIndex() {
		$end_index = $this->getBeginIndex() + $this->per_page_items;
		return min($end_index, $this->total_items);
	}

}

?>