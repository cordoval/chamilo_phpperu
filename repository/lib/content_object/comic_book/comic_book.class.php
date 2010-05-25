<?php
/**
 * This class describes a ComicBook data object
 *
 * @package repository.lib.content_object.comic_book
 * @author Hans De Bisschop
 */

class ComicBook extends ContentObject
{
	const CLASS_NAME = __CLASS__;

	/**
	 * ComicBook properties
	 */
	const PROPERTY_ORIGINAL_TITLE = 'original_title';
	const PROPERTY_ISSUE = 'issue';
	const PROPERTY_SERIES = 'series';
	const PROPERTY_ORIGINAL_SERIES = 'original_series';
	const PROPERTY_SUBSERIES = 'subseries';
	const PROPERTY_ARTIST = 'artist';
	const PROPERTY_WRITER = 'writer';
	const PROPERTY_COLORIST = 'colorist';
	const PROPERTY_INKER = 'inker';
	const PROPERTY_EDITOR = 'editor';
	const PROPERTY_COLLECTION = 'collection';
	const PROPERTY_COLLECTION_ISSUE = 'collection_issue';
	const PROPERTY_TYPE = 'type';
	const PROPERTY_BINDING = 'binding';
	const PROPERTY_PAGES = 'pages';
	const PROPERTY_YEAR = 'year';
	const PROPERTY_GENRE = 'genre';
	const PROPERTY_LIMITED = 'limited';
	const PROPERTY_SIGNED = 'signed';
	const PROPERTY_LANGUAGE = 'language';
	const PROPERTY_COLOUR = 'colour';
	const PROPERTY_WEIGHT = 'weight';
	const PROPERTY_PRICE = 'price';
	const PROPERTY_CURRENCY = 'currency';
	const PROPERTY_SYNOPSIS = 'synopsis';
	const PROPERTY_REVIEW = 'review';
	const PROPERTY_COVERS = 'covers';
	const PROPERTY_EXTRACTS = 'extracts';
	const PROPERTY_TAGS = 'tags';

	/**
	 * Get the additional properties
	 * @return array The property names.
	 */
	static function get_additional_property_names()
	{
		return array (self :: PROPERTY_ORIGINAL_TITLE, self :: PROPERTY_ISSUE, self :: PROPERTY_SERIES, self :: PROPERTY_ORIGINAL_SERIES, self :: PROPERTY_SUBSERIES, self :: PROPERTY_ARTIST, self :: PROPERTY_WRITER, self :: PROPERTY_COLORIST, self :: PROPERTY_INKER, self :: PROPERTY_EDITOR, self :: PROPERTY_COLLECTION, self :: PROPERTY_COLLECTION_ISSUE, self :: PROPERTY_TYPE, self :: PROPERTY_BINDING, self :: PROPERTY_PAGES, self :: PROPERTY_YEAR, self :: PROPERTY_GENRE, self :: PROPERTY_LIMITED, self :: PROPERTY_SIGNED, self :: PROPERTY_LANGUAGE, self :: PROPERTY_COLOUR, self :: PROPERTY_WEIGHT, self :: PROPERTY_PRICE, self :: PROPERTY_CURRENCY, self :: PROPERTY_SYNOPSIS, self :: PROPERTY_REVIEW, self :: PROPERTY_COVERS, self :: PROPERTY_EXTRACTS, self :: PROPERTY_TAGS);
	}

	/**
	 * Returns the original_title of this ComicBook.
	 * @return the original_title.
	 */
	function get_original_title()
	{
		return $this->get_additional_property(self :: PROPERTY_ORIGINAL_TITLE);
	}

	/**
	 * Sets the original_title of this ComicBook.
	 * @param original_title
	 */
	function set_original_title($original_title)
	{
		$this->set_additional_property(self :: PROPERTY_ORIGINAL_TITLE, $original_title);
	}

	/**
	 * Returns the issue of this ComicBook.
	 * @return the issue.
	 */
	function get_issue()
	{
		return $this->get_additional_property(self :: PROPERTY_ISSUE);
	}

	/**
	 * Sets the issue of this ComicBook.
	 * @param issue
	 */
	function set_issue($issue)
	{
		$this->set_additional_property(self :: PROPERTY_ISSUE, $issue);
	}

	/**
	 * Returns the series of this ComicBook.
	 * @return the series.
	 */
	function get_series()
	{
		return $this->get_additional_property(self :: PROPERTY_SERIES);
	}

	/**
	 * Sets the series of this ComicBook.
	 * @param series
	 */
	function set_series($series)
	{
		$this->set_additional_property(self :: PROPERTY_SERIES, $series);
	}

	/**
	 * Returns the original_series of this ComicBook.
	 * @return the original_series.
	 */
	function get_original_series()
	{
		return $this->get_additional_property(self :: PROPERTY_ORIGINAL_SERIES);
	}

	/**
	 * Sets the original_series of this ComicBook.
	 * @param original_series
	 */
	function set_original_series($original_series)
	{
		$this->set_additional_property(self :: PROPERTY_ORIGINAL_SERIES, $original_series);
	}

	/**
	 * Returns the subseries of this ComicBook.
	 * @return the subseries.
	 */
	function get_subseries()
	{
		return $this->get_additional_property(self :: PROPERTY_SUBSERIES);
	}

	/**
	 * Sets the subseries of this ComicBook.
	 * @param subseries
	 */
	function set_subseries($subseries)
	{
		$this->set_additional_property(self :: PROPERTY_SUBSERIES, $subseries);
	}

	/**
	 * Returns the artist of this ComicBook.
	 * @return the artist.
	 */
	function get_artist()
	{
		return $this->get_additional_property(self :: PROPERTY_ARTIST);
	}

	/**
	 * Sets the artist of this ComicBook.
	 * @param artist
	 */
	function set_artist($artist)
	{
		$this->set_additional_property(self :: PROPERTY_ARTIST, $artist);
	}

	/**
	 * Returns the writer of this ComicBook.
	 * @return the writer.
	 */
	function get_writer()
	{
		return $this->get_additional_property(self :: PROPERTY_WRITER);
	}

	/**
	 * Sets the writer of this ComicBook.
	 * @param writer
	 */
	function set_writer($writer)
	{
		$this->set_additional_property(self :: PROPERTY_WRITER, $writer);
	}

	/**
	 * Returns the colorist of this ComicBook.
	 * @return the colorist.
	 */
	function get_colorist()
	{
		return $this->get_additional_property(self :: PROPERTY_COLORIST);
	}

	/**
	 * Sets the colorist of this ComicBook.
	 * @param colorist
	 */
	function set_colorist($colorist)
	{
		$this->set_additional_property(self :: PROPERTY_COLORIST, $colorist);
	}

	/**
	 * Returns the inker of this ComicBook.
	 * @return the inker.
	 */
	function get_inker()
	{
		return $this->get_additional_property(self :: PROPERTY_INKER);
	}

	/**
	 * Sets the inker of this ComicBook.
	 * @param inker
	 */
	function set_inker($inker)
	{
		$this->set_additional_property(self :: PROPERTY_INKER, $inker);
	}

	/**
	 * Returns the editor of this ComicBook.
	 * @return the editor.
	 */
	function get_editor()
	{
		return $this->get_additional_property(self :: PROPERTY_EDITOR);
	}

	/**
	 * Sets the editor of this ComicBook.
	 * @param editor
	 */
	function set_editor($editor)
	{
		$this->set_additional_property(self :: PROPERTY_EDITOR, $editor);
	}

	/**
	 * Returns the collection of this ComicBook.
	 * @return the collection.
	 */
	function get_collection()
	{
		return $this->get_additional_property(self :: PROPERTY_COLLECTION);
	}

	/**
	 * Sets the collection of this ComicBook.
	 * @param collection
	 */
	function set_collection($collection)
	{
		$this->set_additional_property(self :: PROPERTY_COLLECTION, $collection);
	}

	/**
	 * Returns the collection_issue of this ComicBook.
	 * @return the collection_issue.
	 */
	function get_collection_issue()
	{
		return $this->get_additional_property(self :: PROPERTY_COLLECTION_ISSUE);
	}

	/**
	 * Sets the collection_issue of this ComicBook.
	 * @param collection_issue
	 */
	function set_collection_issue($collection_issue)
	{
		$this->set_additional_property(self :: PROPERTY_COLLECTION_ISSUE, $collection_issue);
	}

	/**
	 * Returns the type of this ComicBook.
	 * @return the type.
	 */
	function get_type()
	{
		return $this->get_additional_property(self :: PROPERTY_TYPE);
	}

	/**
	 * Sets the type of this ComicBook.
	 * @param type
	 */
	function set_type($type)
	{
		$this->set_additional_property(self :: PROPERTY_TYPE, $type);
	}

	/**
	 * Returns the binding of this ComicBook.
	 * @return the binding.
	 */
	function get_binding()
	{
		return $this->get_additional_property(self :: PROPERTY_BINDING);
	}

	/**
	 * Sets the binding of this ComicBook.
	 * @param binding
	 */
	function set_binding($binding)
	{
		$this->set_additional_property(self :: PROPERTY_BINDING, $binding);
	}

	/**
	 * Returns the pages of this ComicBook.
	 * @return the pages.
	 */
	function get_pages()
	{
		return $this->get_additional_property(self :: PROPERTY_PAGES);
	}

	/**
	 * Sets the pages of this ComicBook.
	 * @param pages
	 */
	function set_pages($pages)
	{
		$this->set_additional_property(self :: PROPERTY_PAGES, $pages);
	}

	/**
	 * Returns the year of this ComicBook.
	 * @return the year.
	 */
	function get_year()
	{
		return $this->get_additional_property(self :: PROPERTY_YEAR);
	}

	/**
	 * Sets the year of this ComicBook.
	 * @param year
	 */
	function set_year($year)
	{
		$this->set_additional_property(self :: PROPERTY_YEAR, $year);
	}

	/**
	 * Returns the genre of this ComicBook.
	 * @return the genre.
	 */
	function get_genre()
	{
		return $this->get_additional_property(self :: PROPERTY_GENRE);
	}

	/**
	 * Sets the genre of this ComicBook.
	 * @param genre
	 */
	function set_genre($genre)
	{
		$this->set_additional_property(self :: PROPERTY_GENRE, $genre);
	}

	/**
	 * Returns the limited of this ComicBook.
	 * @return the limited.
	 */
	function get_limited()
	{
		return $this->get_additional_property(self :: PROPERTY_LIMITED);
	}

	/**
	 * Sets the limited of this ComicBook.
	 * @param limited
	 */
	function set_limited($limited)
	{
		$this->set_additional_property(self :: PROPERTY_LIMITED, $limited);
	}

	/**
	 * Returns the signed of this ComicBook.
	 * @return the signed.
	 */
	function get_signed()
	{
		return $this->get_additional_property(self :: PROPERTY_SIGNED);
	}

	/**
	 * Sets the signed of this ComicBook.
	 * @param signed
	 */
	function set_signed($signed)
	{
		$this->set_additional_property(self :: PROPERTY_SIGNED, $signed);
	}

	/**
	 * Returns the language of this ComicBook.
	 * @return the language.
	 */
	function get_language()
	{
		return $this->get_additional_property(self :: PROPERTY_LANGUAGE);
	}

	/**
	 * Sets the language of this ComicBook.
	 * @param language
	 */
	function set_language($language)
	{
		$this->set_additional_property(self :: PROPERTY_LANGUAGE, $language);
	}

	/**
	 * Returns the colour of this ComicBook.
	 * @return the colour.
	 */
	function get_colour()
	{
		return $this->get_additional_property(self :: PROPERTY_COLOUR);
	}

	/**
	 * Sets the colour of this ComicBook.
	 * @param colour
	 */
	function set_colour($colour)
	{
		$this->set_additional_property(self :: PROPERTY_COLOUR, $colour);
	}

	/**
	 * Returns the weight of this ComicBook.
	 * @return the weight.
	 */
	function get_weight()
	{
		return $this->get_additional_property(self :: PROPERTY_WEIGHT);
	}

	/**
	 * Sets the weight of this ComicBook.
	 * @param weight
	 */
	function set_weight($weight)
	{
		$this->set_additional_property(self :: PROPERTY_WEIGHT, $weight);
	}

	/**
	 * Returns the price of this ComicBook.
	 * @return the price.
	 */
	function get_price()
	{
		return $this->get_additional_property(self :: PROPERTY_PRICE);
	}

	/**
	 * Sets the price of this ComicBook.
	 * @param price
	 */
	function set_price($price)
	{
		$this->set_additional_property(self :: PROPERTY_PRICE, $price);
	}

	/**
	 * Returns the currency of this ComicBook.
	 * @return the currency.
	 */
	function get_currency()
	{
		return $this->get_additional_property(self :: PROPERTY_CURRENCY);
	}

	/**
	 * Sets the currency of this ComicBook.
	 * @param currency
	 */
	function set_currency($currency)
	{
		$this->set_additional_property(self :: PROPERTY_CURRENCY, $currency);
	}

	/**
	 * Returns the synopsis of this ComicBook.
	 * @return the synopsis.
	 */
	function get_synopsis()
	{
		return $this->get_additional_property(self :: PROPERTY_SYNOPSIS);
	}

	/**
	 * Sets the synopsis of this ComicBook.
	 * @param synopsis
	 */
	function set_synopsis($synopsis)
	{
		$this->set_additional_property(self :: PROPERTY_SYNOPSIS, $synopsis);
	}

	/**
	 * Returns the review of this ComicBook.
	 * @return the review.
	 */
	function get_review()
	{
		return $this->get_additional_property(self :: PROPERTY_REVIEW);
	}

	/**
	 * Sets the review of this ComicBook.
	 * @param review
	 */
	function set_review($review)
	{
		$this->set_additional_property(self :: PROPERTY_REVIEW, $review);
	}

	/**
	 * Returns the covers of this ComicBook.
	 * @return the covers.
	 */
	function get_covers()
	{
		return $this->get_additional_property(self :: PROPERTY_COVERS);
	}

	/**
	 * Sets the covers of this ComicBook.
	 * @param covers
	 */
	function set_covers($covers)
	{
		$this->set_additional_property(self :: PROPERTY_COVERS, $covers);
	}

	/**
	 * Returns the extracts of this ComicBook.
	 * @return the extracts.
	 */
	function get_extracts()
	{
		return $this->get_additional_property(self :: PROPERTY_EXTRACTS);
	}

	/**
	 * Sets the extracts of this ComicBook.
	 * @param extracts
	 */
	function set_extracts($extracts)
	{
		$this->set_additional_property(self :: PROPERTY_EXTRACTS, $extracts);
	}

	/**
	 * Returns the tags of this ComicBook.
	 * @return the tags.
	 */
	function get_tags()
	{
		return $this->get_additional_property(self :: PROPERTY_TAGS);
	}

	/**
	 * Sets the tags of this ComicBook.
	 * @param tags
	 */
	function set_tags($tags)
	{
		$this->set_additional_property(self :: PROPERTY_TAGS, $tags);
	}


	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
?>