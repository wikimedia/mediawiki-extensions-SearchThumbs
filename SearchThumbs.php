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
		$services = MediaWikiServices::getInstance();
		$provider = $services->getConnectionProvider();
		$dbr = $provider->getReplicaDatabase();
		$image = $dbr->newSelectQueryBuilder()
			->select( 'pp_value' )
			->from( 'page_props' )
			->where( [ 'pp_propname' => 'page_image_free', 'pp_page' => $id ] )
			->fetchField();
		if ( $image ) {
			$file = $services->getRepoGroup()->findFile( $image );
			if ( $file ) {
				$thumb = $file->createThumb( 120 );
				$img = '<img class="mw-search-result-thumb" src="' . $thumb . '" />';
				$link = $img . $link;
			}
		}
	}
}
