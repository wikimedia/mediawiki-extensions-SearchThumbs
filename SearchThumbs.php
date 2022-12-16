<?php

use MediaWiki\MediaWikiServices;

class SearchThumbs {

	/**
	 * @param OutputPage &$out
	 * @param Skin &$skin
	 */
	public static function onBeforePageDisplay( OutputPage &$out, Skin &$skin ) {
		$out->addModuleStyles( 'ext.SearchThumbs' );
	}

	/**
	 * @param SpecialSearch $searchPage
	 * @param SearchResult $result
	 * @param string[] $terms
	 * @param string &$link
	 * @param string &$redirect
	 * @param string &$section
	 * @param string &$extract
	 * @param string $score
	 * @param string &$size
	 * @param string &$date
	 * @param string &$related
	 * @param string &$html
	 */
	public static function onShowSearchHit(
		$searchPage,
		$result,
		$terms,
		&$link,
		&$redirect,
		&$section,
		&$extract,
		$score,
		&$size,
		&$date,
		&$related,
		&$html
	) {
		$id = $result->getTitle()->getArticleID();
		$dbr = wfGetDB( DB_REPLICA );
		$result = $dbr->select( 'page_props', 'pp_value', [ 'pp_propname = "page_image_free"', "pp_page = $id" ] );
		$image = null;
		foreach ( $result as $row ) {
			$image = $row->pp_value;
		}
		if ( $image ) {
			$file = MediaWikiServices::getInstance()->getRepoGroup()->getLocalRepo()->findFile( $image );
			if ( $file ) {
				$thumb = '<img class="mw-search-result-thumb" src="' . $file->createThumb( 120 ) . '" />';
				$link = $thumb . $link;
			}
		}
	}
}
