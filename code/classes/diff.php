<?php
mb_internal_encoding('UTF-8');

class DifferenceEngine {

	var $mOldid, $mNewid, $mTitle;
	var $mOldtitle, $mNewtitle, $mPagetitle;
	var $mOldtext, $mNewtext;
	var $mOldPage, $mNewPage;
	var $mRcidMarkPatrolled;
	var $mOldRev, $mNewRev;
	var $mRevisionsLoaded = false; // Have the revisions been loaded
	var $mTextLoaded = 0; // How many text blobs have been loaded, 0, 1 or 2?
	/**
	 * #@-
	 */

	function DifferenceEngine() {
	}

	/**
	 * Get the diff text, send it to $wgOut
	 * Returns false if the diff could not be generated, otherwise returns true
	 */
	function showDiff($otitle, $ntitle) {

		$diff = $this->getDiff($otitle, $ntitle);
		if($diff === false){
			// /Error MSG
			return false;
		}else{
			$this->showDiffStyle();
			// ~ echo $diff;

			return $diff;
		}
	}

	/**
	 * Add style sheets and supporting JS for diff display.
	 */
	function showDiffStyle() {
		// ~ global $wgStylePath, $wgStyleVersion, $wgOut;
		// ~ $wgOut->addStyle( 'common/diff.css' );

		// ~ // JS is needed to detect old versions of Mozilla to work around an
		// annoyance bug.
		// ~ $wgOut->addScript( "<script type=\"text/javascript\"
		// src=\"$wgStylePath/common/diff.js?$wgStyleVersion\"></script>" );
	}

	function getDiff($otitle, $ntitle) {
		$this->mOldtext = $otitle;
		$this->mNewtext = $ntitle;
		$body = $this->getDiffBody();

		if($body === false){
			return false;
		}else{
			// ~ $multi = $this->getMultiNotice();
			return $this->addHeader($body, $otitle, $ntitle, $multi);
		}
	}

	/**
	 * Get the diff table body, without header
	 *
	 * @return mixed
	 */
	function getDiffBody() {
		$difftext = $this->generateDiffBody($this->mOldtext, $this->mNewtext);

		if($difftext !== false){
			$difftext = $this->localiseLineNumbers($difftext);
		}
		return $difftext;
	}

	/**
	 * Generate a diff, no caching
	 * $otext and $ntext must be already segmented
	 */
	function generateDiffBody($otext, $ntext) {
		$otext = str_replace("\r\n", "\n", $otext);
		$ntext = str_replace("\r\n", "\n", $ntext);

		$ota = explode("\n", $otext);
		$nta = explode("\n", $ntext);
		// ~ var_dump( $ntext );
		$diffs = new Diff($ota, $nta);
		// ~ echo $diffs;
		$formatter = new TableDiffFormatter();
		return $formatter->format($diffs);

	}

	/**
	 * Replace line numbers with the text in the user's language
	 */
	function localiseLineNumbers($text) {
		return preg_replace_callback('/<!--LINE (\d+)-->/', array(
			&$this,
			'localiseLineNumbersCb'
		), $text);
	}

	function localiseLineNumbersCb($matches) {
		global $wgLang;
		// ~ var_dump( $matches );
		return 'Line ' . $matches[1];
		// ~ return wfMsgExt( 'lineno', array('parseinline'),
		// $wgLang->formatNum( $matches[1] ) );
	}

	/**
	 * Add the header to a diff body
	 */
	static function addHeader($diff, $otitle, $ntitle, $multi = '') {
		global $wgOut;
		// ~ var_dump( $diff );
		$header = "
			<table class='diff'>
			<col class='diff-marker' />
			<col class='diff-content' />
			<col class='diff-marker' />
			<col class='diff-content' />
		";

		if($multi != '')
			$header .= "<tr><td colspan='4' align='center' class='diff-multi'>{$multi}</td></tr>";

		return $header . $diff . "</table>";
	}

	/**
	 * Use specified text instead of loading from the database
	 */
	function setText($oldText, $newText) {
		$this->mOldtext = $oldText;
		$this->mNewtext = $newText;
		$this->mTextLoaded = 2;
	}

}

// A PHP diff engine for phpwiki. (Taken from phpwiki-1.3.3)
//
// Copyright (C) 2000, 2001 Geoffrey T. Dairiki <dairiki@dairiki.org>
// You may copy this code freely under the conditions of the GPL.
//

define('USE_ASSERTS', function_exists('assert'));

/**
 *
 * @todo document
 *       @private
 *       @addtogroup DifferenceEngine
 */
class _DiffOp {
	var $type;
	var $orig;
	var $closing;
	var $start_pos;
	var $end_pos;

	function reverse() {
		trigger_error('pure virtual', E_USER_ERROR);
	}

	function setStartPos($pPos) {
		$this->start_pos = $pPos;
	}

	function getStartPos() {
		return $this->start_pos;
	}

	function getEndPos() {
		return $this->end_pos;
	}

	function setEndPos($pPos) {
		$this->end_pos = $pPos;
	}

	function norig() {
		return $this->orig ? sizeof($this->orig) : 0;
	}

	function nclosing() {
		return $this->closing ? sizeof($this->closing) : 0;
	}
}

/**
 *
 * @todo document
 *       @private
 *       @addtogroup DifferenceEngine
 */
class _DiffOp_Copy extends _DiffOp {
	var $type = 'copy';

	function _DiffOp_Copy($orig, $closing = false) {
		if(! is_array($closing))
			$closing = $orig;
		$this->orig = $orig;
		$this->closing = $closing;
	}

	function reverse() {
		return new _DiffOp_Copy($this->closing, $this->orig);
	}
}

/**
 *
 * @todo document
 *       @private
 *       @addtogroup DifferenceEngine
 */
class _DiffOp_Delete extends _DiffOp {
	var $type = 'delete';

	function _DiffOp_Delete($lines) {
		$this->orig = $lines;
		$this->closing = false;
	}

	function reverse() {
		return new _DiffOp_Add($this->orig);
	}
}

/**
 *
 * @todo document
 *       @private
 *       @addtogroup DifferenceEngine
 */
class _DiffOp_Add extends _DiffOp {
	var $type = 'add';

	function _DiffOp_Add($lines) {
		$this->closing = $lines;
		$this->orig = false;
	}

	function reverse() {
		return new _DiffOp_Delete($this->closing);
	}
}

/**
 *
 * @todo document
 *       @private
 *       @addtogroup DifferenceEngine
 */
class _DiffOp_Change extends _DiffOp {
	var $type = 'change';

	function _DiffOp_Change($orig, $closing) {
		$this->orig = $orig;
		$this->closing = $closing;
	}

	function reverse() {
		return new _DiffOp_Change($this->closing, $this->orig);
	}
}

/**
 * Class used internally by Diff to actually compute the diffs.
 *
 * The algorithm used here is mostly lifted from the perl module
 * Algorithm::Diff (version 1.06) by Ned Konz, which is available at:
 * http://www.perl.com/CPAN/authors/id/N/NE/NEDKONZ/Algorithm-Diff-1.06.zip
 *
 * More ideas are taken from:
 * http://www.ics.uci.edu/~eppstein/161/960229.html
 *
 * Some ideas are (and a bit of code) are from from analyze.c, from GNU
 * diffutils-2.7, which can be found at:
 * ftp://gnudist.gnu.org/pub/gnu/diffutils/diffutils-2.7.tar.gz
 *
 * closingly, some ideas (subdivision by NCHUNKS > 2, and some optimizations)
 * are my own.
 *
 * Line length limits for robustness added by Tim Starling, 2005-08-31
 *
 * @author Geoffrey T. Dairiki, Tim Starling
 *         @private
 *         @addtogroup DifferenceEngine
 */
class _DiffEngine {
	const MAX_XREF_LENGTH = 10000;

	function diff($from_lines, $to_lines) {

		$n_from = sizeof($from_lines);
		$n_to = sizeof($to_lines);

		$this->xchanged = $this->ychanged = array();
		$this->xv = $this->yv = array();
		$this->xind = $this->yind = array();
		unset($this->seq);
		unset($this->in_seq);
		unset($this->lcs);

		// Skip leading common lines.
		for($skip = 0; $skip < $n_from && $skip < $n_to; $skip ++){
			if($from_lines[$skip] !== $to_lines[$skip])
				break;
			$this->xchanged[$skip] = $this->ychanged[$skip] = false;
		}
		// Skip trailing common lines.
		$xi = $n_from;
		$yi = $n_to;
		for($endskip = 0; -- $xi > $skip && -- $yi > $skip; $endskip ++){
			if($from_lines[$xi] !== $to_lines[$yi])
				break;
			$this->xchanged[$xi] = $this->ychanged[$yi] = false;
		}

		// Ignore lines which do not exist in both files.
		for($xi = $skip; $xi < $n_from - $endskip; $xi ++){
			$xhash[$this->_line_hash($from_lines[$xi])] = 1;
		}

		for($yi = $skip; $yi < $n_to - $endskip; $yi ++){
			$line = $to_lines[$yi];
			if(($this->ychanged[$yi] = empty($xhash[$this->_line_hash($line)])))
				continue;
			$yhash[$this->_line_hash($line)] = 1;
			$this->yv[] = $line;
			$this->yind[] = $yi;
		}
		for($xi = $skip; $xi < $n_from - $endskip; $xi ++){
			$line = $from_lines[$xi];
			if(($this->xchanged[$xi] = empty($yhash[$this->_line_hash($line)])))
				continue;
			$this->xv[] = $line;
			$this->xind[] = $xi;
		}

		// Find the LCS.
		$this->_compareseq(0, sizeof($this->xv), 0, sizeof($this->yv));

		// Merge edits when possible
		$this->_shift_boundaries($from_lines, $this->xchanged, $this->ychanged);
		$this->_shift_boundaries($to_lines, $this->ychanged, $this->xchanged);

		// Compute the edit operations.
		$edits = array();
		$xi = $yi = 0;
		while($xi < $n_from || $yi < $n_to){
			USE_ASSERTS && assert($yi < $n_to || $this->xchanged[$xi]);
			USE_ASSERTS && assert($xi < $n_from || $this->ychanged[$yi]);

			// Skip matching "snake".
			$copy = array();
			while($xi < $n_from && $yi < $n_to && ! $this->xchanged[$xi] && ! $this->ychanged[$yi]){
				$copy[] = $from_lines[$xi ++];
				++ $yi;
			}
			if($copy)
				$edits[] = new _DiffOp_Copy($copy);

				// Find deletes & adds.
			$delete = array();
			while($xi < $n_from && $this->xchanged[$xi])
				$delete[] = $from_lines[$xi ++];

			$add = array();
			while($yi < $n_to && $this->ychanged[$yi])
				$add[] = $to_lines[$yi ++];

			if($delete && $add)
				$edits[] = new _DiffOp_Change($delete, $add);
			elseif($delete)
				$edits[] = new _DiffOp_Delete($delete);
			elseif($add)
				$edits[] = new _DiffOp_Add($add);
		}

		return $edits;
	}

	/**
	 * Returns the whole line if it's small enough, or the MD5 hash otherwise
	 */
	function _line_hash($line) {
		if(strlen($line) > self::MAX_XREF_LENGTH){
			return md5($line);
		}else{
			return $line;
		}
	}

	/*
	 * Divide the Largest Common Subsequence (LCS) of the sequences [XOFF, XLIM)
	 * and [YOFF, YLIM) into NCHUNKS approximately equally sized segments.
	 * Returns (LCS, PTS).	LCS is the length of the LCS. PTS is an array of
	 * NCHUNKS+1 (X, Y) indexes giving the diving points between sub sequences.
	 * The first sub-sequence is contained in [X0, X1), [Y0, Y1), the second in
	 * [X1, X2), [Y1, Y2) and so on. Note that (X0, Y0) == (XOFF, YOFF) and
	 * (X[NCHUNKS], Y[NCHUNKS]) == (XLIM, YLIM). This function assumes that the
	 * first lines of the specified portions of the two files do not match, and
	 * likewise that the last lines do not match. The caller must trim matching
	 * lines from the beginning and end of the portions it is going to specify.
	 */
	function _diag($xoff, $xlim, $yoff, $ylim, $nchunks) {
		$flip = false;

		if($xlim - $xoff > $ylim - $yoff){
			// Things seems faster (I'm not sure I understand why)
			// when the shortest sequence in X.
			$flip = true;
			list($xoff, $xlim, $yoff, $ylim) = array(
				$yoff,
				$ylim,
				$xoff,
				$xlim
			);
		}

		if($flip)
			for($i = $ylim - 1; $i >= $yoff; $i --)
				$ymatches[$this->xv[$i]][] = $i;
		else
			for($i = $ylim - 1; $i >= $yoff; $i --)
				$ymatches[$this->yv[$i]][] = $i;

		$this->lcs = 0;
		$this->seq[0] = $yoff - 1;
		$this->in_seq = array();
		$ymids[0] = array();

		$numer = $xlim - $xoff + $nchunks - 1;
		$x = $xoff;
		for($chunk = 0; $chunk < $nchunks; $chunk ++){

			if($chunk > 0)
				for($i = 0; $i <= $this->lcs; $i ++)
					$ymids[$i][$chunk - 1] = $this->seq[$i];

			$x1 = $xoff + (int) (($numer + ($xlim - $xoff) * $chunk) / $nchunks);
			for(; $x < $x1; $x ++){
				$line = $flip ? $this->yv[$x] : $this->xv[$x];
				if(empty($ymatches[$line]))
					continue;
				$matches = $ymatches[$line];
				reset($matches);
				while(list($junk, $y) = each($matches))
					if(empty($this->in_seq[$y])){
						$k = $this->_lcs_pos($y);
						USE_ASSERTS && assert($k > 0);
						$ymids[$k] = $ymids[$k - 1];
						break;
					}
				while(list( /* $junk */, $y) = each($matches)){
					if($y > $this->seq[$k - 1]){
						USE_ASSERTS && assert($y < $this->seq[$k]);
						// Optimization: this is a common case:
						// next match is just replacing previous match.
						$this->in_seq[$this->seq[$k]] = false;
						$this->seq[$k] = $y;
						$this->in_seq[$y] = 1;
					}else if(empty($this->in_seq[$y])){
						$k = $this->_lcs_pos($y);
						USE_ASSERTS && assert($k > 0);
						$ymids[$k] = $ymids[$k - 1];
					}
				}
			}

		}

		$seps[] = $flip ? array(
			$yoff,
			$xoff
		) : array(
			$xoff,
			$yoff
		);
		$ymid = $ymids[$this->lcs];
		for($n = 0; $n < $nchunks - 1; $n ++){
			$x1 = $xoff + (int) (($numer + ($xlim - $xoff) * $n) / $nchunks);
			$y1 = $ymid[$n] + 1;
			$seps[] = $flip ? array(
				$y1,
				$x1
			) : array(
				$x1,
				$y1
			);
		}
		$seps[] = $flip ? array(
			$ylim,
			$xlim
		) : array(
			$xlim,
			$ylim
		);

		return array(
			$this->lcs,
			$seps
		);
	}

	function _lcs_pos($ypos) {

		$end = $this->lcs;
		if($end == 0 || $ypos > $this->seq[$end]){
			$this->seq[++ $this->lcs] = $ypos;
			$this->in_seq[$ypos] = 1;

			return $this->lcs;
		}

		$beg = 1;
		while($beg < $end){
			$mid = (int) (($beg + $end) / 2);
			if($ypos > $this->seq[$mid])
				$beg = $mid + 1;
			else
				$end = $mid;
		}

		USE_ASSERTS && assert($ypos != $this->seq[$end]);

		$this->in_seq[$this->seq[$end]] = false;
		$this->seq[$end] = $ypos;
		$this->in_seq[$ypos] = 1;

		return $end;
	}

	/*
	 * Find LCS of two sequences. The results are recorded in the vectors
	 * $this->{x,y}changed[], by storing a 1 in the element for each line that
	 * is an insertion or deletion (ie. is not in the LCS). The subsequence of
	 * file 0 is [XOFF, XLIM) and likewise for file 1. Note that XLIM, YLIM are
	 * exclusive bounds. All line numbers are origin-0 and discarded lines are
	 * not counted.
	 */
	function _compareseq($xoff, $xlim, $yoff, $ylim) {

		// Slide down the bottom initial diagonal.
		while($xoff < $xlim && $yoff < $ylim && $this->xv[$xoff] == $this->yv[$yoff]){
			++ $xoff;
			++ $yoff;
		}

		// Slide up the top initial diagonal.
		while($xlim > $xoff && $ylim > $yoff && $this->xv[$xlim - 1] == $this->yv[$ylim - 1]){
			-- $xlim;
			-- $ylim;
		}

		if($xoff == $xlim || $yoff == $ylim)
			$lcs = 0;
		else{
			// This is ad hoc but seems to work well.
			// $nchunks = sqrt(min($xlim - $xoff, $ylim - $yoff) / 2.5);
			// $nchunks = max(2,min(8,(int)$nchunks));
			$nchunks = min(7, $xlim - $xoff, $ylim - $yoff) + 1;
			list($lcs, $seps) = $this->_diag($xoff, $xlim, $yoff, $ylim, $nchunks);
		}

		if($lcs == 0){
			// X and Y sequences have no common subsequence:
			// mark all changed.
			while($yoff < $ylim)
				$this->ychanged[$this->yind[$yoff ++]] = 1;
			while($xoff < $xlim)
				$this->xchanged[$this->xind[$xoff ++]] = 1;
		}else{
			// Use the partitions to split this problem into subproblems.
			reset($seps);
			$pt1 = $seps[0];
			while($pt2 = next($seps)){
				$this->_compareseq($pt1[0], $pt2[0], $pt1[1], $pt2[1]);
				$pt1 = $pt2;
			}
		}

	}

	/*
	 * Adjust inserts/deletes of identical lines to join changes as much as
	 * possible. We do something when a run of changed lines include a line at
	 * one end and has an excluded, identical line at the other. We are free to
	 * choose which identical line is included. `compareseq' usually chooses the
	 * one at the beginning, but usually it is cleaner to consider the following
	 * identical line to be the "change". This is extracted verbatim from
	 * analyze.c (GNU diffutils-2.7).
	 */
	function _shift_boundaries($lines, &$changed, $other_changed) {
		$i = 0;
		$j = 0;

		USE_ASSERTS && assert('sizeof($lines) == sizeof($changed)');
		$len = sizeof($lines);
		$other_len = sizeof($other_changed);

		while(1){
			/*
			 * Scan forwards to find beginning of another run of changes. Also
			 * keep track of the corresponding point in the other file.
			 * Throughout this code, $i and $j are adjusted together so that the
			 * first $i elements of $changed and the first $j elements of
			 * $other_changed both contain the same number of zeros (unchanged
			 * lines). Furthermore, $j is always kept so that $j == $other_len
			 * or $other_changed[$j] == false.
			 */
			while($j < $other_len && $other_changed[$j])
				$j ++;

			while($i < $len && ! $changed[$i]){
				USE_ASSERTS && assert('$j < $other_len && ! $other_changed[$j]');
				$i ++;
				$j ++;
				while($j < $other_len && $other_changed[$j])
					$j ++;
			}

			if($i == $len)
				break;

			$start = $i;

			// Find the end of this run of changes.
			while(++ $i < $len && $changed[$i])
				continue;

			do{
				/*
				 * Record the length of this run of changes, so that we can
				 * later determine whether the run has grown.
				 */
				$runlength = $i - $start;

				/*
				 * Move the changed region back, so long as the previous
				 * unchanged line matches the last changed one. This merges with
				 * previous changed regions.
				 */
				while($start > 0 && $lines[$start - 1] == $lines[$i - 1]){
					$changed[-- $start] = 1;
					$changed[-- $i] = false;
					while($start > 0 && $changed[$start - 1])
						$start --;
					USE_ASSERTS && assert('$j > 0');
					while($other_changed[-- $j])
						continue;
					USE_ASSERTS && assert('$j >= 0 && !$other_changed[$j]');
				}

				/*
				 * Set CORRESPONDING to the end of the changed run, at the last
				 * point where it corresponds to a changed run in the other
				 * file. CORRESPONDING == LEN means no such point has been
				 * found.
				 */
				$corresponding = $j < $other_len ? $i : $len;

				/*
				 * Move the changed region forward, so long as the first changed
				 * line matches the following unchanged one. This merges with
				 * following changed regions. Do this second, so that if there
				 * are no merges, the changed region is moved forward as far as
				 * possible.
				 */
				while($i < $len && $lines[$start] == $lines[$i]){
					$changed[$start ++] = false;
					$changed[$i ++] = 1;
					while($i < $len && $changed[$i])
						$i ++;

					USE_ASSERTS && assert('$j < $other_len && ! $other_changed[$j]');
					$j ++;
					if($j < $other_len && $other_changed[$j]){
						$corresponding = $i;
						while($j < $other_len && $other_changed[$j])
							$j ++;
					}
				}
			}while($runlength != $i - $start);

			/*
			 * If possible, move the fully-merged run of changes back to a
			 * corresponding run in the other file.
			 */
			while($corresponding < $i){
				$changed[-- $start] = 1;
				$changed[-- $i] = 0;
				USE_ASSERTS && assert('$j > 0');
				while($other_changed[-- $j])
					continue;
				USE_ASSERTS && assert('$j >= 0 && !$other_changed[$j]');
			}
		}

	}
}

/**
 * Class representing a 'diff' between two sequences of strings.
 *
 * @todo document
 *       @private
 *       @addtogroup DifferenceEngine
 */
class Diff {
	var $edits;
	var $m_diffType;

	/**
	 * Constructor.
	 * Computes diff between sequences of strings.
	 *
	 * @param $from_lines array
	 *       	 An array of strings.
	 *       	 (Typically these are lines from a file.)
	 * @param $to_lines array
	 *       	 An array of strings.
	 */
	function Diff($from_lines, $to_lines, $pDiffType = DIFF_TYPE) {
		$eng = new _DiffEngine();
		$this->edits = $eng->diff($from_lines, $to_lines);
		$this->m_diffType = $pDiffType;
		// ~ var_dump( $from_lines );
		// $this->_check($from_lines, $to_lines);
	}

	function GetDiffType(){
		return $this->m_diffType;
	}

	/**
	 * Compute reversed Diff.
	 *
	 * SYNOPSIS:
	 *
	 * $diff = new Diff($lines1, $lines2);
	 * $rev = $diff->reverse();
	 *
	 * @return object A Diff object representing the inverse of the
	 *         original diff.
	 */
	function reverse() {
		$rev = $this;
		$rev->edits = array();
		foreach($this->edits as $edit){
			$rev->edits[] = $edit->reverse();
		}
		return $rev;
	}

	/**
	 * Check for empty diff.
	 *
	 * @return bool True iff two sequences were identical.
	 */
	function isEmpty() {
		foreach($this->edits as $edit){
			if($edit->type != 'copy')
				return false;
		}
		return true;
	}

	/**
	 * Compute the length of the Longest Common Subsequence (LCS).
	 *
	 * This is mostly for diagnostic purposed.
	 *
	 * @return int The length of the LCS.
	 */
	function lcs() {
		$lcs = 0;
		foreach($this->edits as $edit){
			if($edit->type == 'copy')
				$lcs += sizeof($edit->orig);
		}
		return $lcs;
	}

	/**
	 * Get the original set of lines.
	 *
	 * This reconstructs the $from_lines parameter passed to the
	 * constructor.
	 *
	 * @return array The original sequence of strings.
	 */
	function orig() {
		$lines = array();

		foreach($this->edits as $edit){
			if($edit->orig)
				array_splice($lines, sizeof($lines), 0, $edit->orig);
		}
		return $lines;
	}

	/**
	 * Get the closing set of lines.
	 *
	 * This reconstructs the $to_lines parameter passed to the
	 * constructor.
	 *
	 * @return array The sequence of strings.
	 */
	function closing() {
		$lines = array();

		foreach($this->edits as $edit){
			if($edit->closing)
				array_splice($lines, sizeof($lines), 0, $edit->closing);
		}
		return $lines;
	}

	/**
	 * Check a Diff for validity.
	 *
	 * This is here only for debugging purposes.
	 */
	function _check($from_lines, $to_lines) {

		if(serialize($from_lines) != serialize($this->orig()))
			trigger_error("Reconstructed original doesn't match", E_USER_ERROR);
		if(serialize($to_lines) != serialize($this->closing()))
			trigger_error("Reconstructed closing doesn't match", E_USER_ERROR);

		$rev = $this->reverse();
		if(serialize($to_lines) != serialize($rev->orig()))
			trigger_error("Reversed original doesn't match", E_USER_ERROR);
		if(serialize($from_lines) != serialize($rev->closing()))
			trigger_error("Reversed closing doesn't match", E_USER_ERROR);

		$prevtype = 'none';
		foreach($this->edits as $edit){
			if($prevtype == $edit->type)
				trigger_error("Edit sequence is non-optimal", E_USER_ERROR);
			$prevtype = $edit->type;
		}

		$lcs = $this->lcs();
		trigger_error('Diff okay: LCS = ' . $lcs, E_USER_NOTICE);

	}
}

/**
 *
 * @todo document, bad name.
 *       @private
 *       @addtogroup DifferenceEngine
 */
class MappedDiff extends Diff {
	/**
	 * Constructor.
	 *
	 * Computes diff between sequences of strings.
	 *
	 * This can be used to compute things like
	 * case-insensitve diffs, or diffs which ignore
	 * changes in white-space.
	 *
	 * @param $from_lines array
	 *       	 An array of strings.
	 *       	 (Typically these are lines from a file.)
	 *
	 * @param $to_lines array
	 *       	 An array of strings.
	 *
	 * @param $mapped_from_lines array
	 *       	 This array should
	 *       	 have the same size number of elements as $from_lines.
	 *       	 The elements in $mapped_from_lines and
	 *       	 $mapped_to_lines are what is actually compared
	 *       	 when computing the diff.
	 *
	 * @param $mapped_to_lines array
	 *       	 This array should
	 *       	 have the same number of elements as $to_lines.
	 */
	function MappedDiff($from_lines, $to_lines, $mapped_from_lines, $mapped_to_lines) {

		assert(sizeof($from_lines) == sizeof($mapped_from_lines));
		assert(sizeof($to_lines) == sizeof($mapped_to_lines));

		$this->Diff($mapped_from_lines, $mapped_to_lines);

		$xi = $yi = 0;
		for($i = 0; $i < sizeof($this->edits); $i ++){
			$orig = &$this->edits[$i]->orig;
			if(is_array($orig)){
				$orig = array_slice($from_lines, $xi, sizeof($orig));
				$xi += sizeof($orig);
			}

			$closing = &$this->edits[$i]->closing;
			if(is_array($closing)){
				$closing = array_slice($to_lines, $yi, sizeof($closing));
				$yi += sizeof($closing);
			}
		}

	}
}

/**
 * A class to format Diffs
 *
 * This class formats the diff in classic diff format.
 * It is intended that this class be customized via inheritance,
 * to obtain fancier outputs.
 *
 * @todo document
 *       @private
 *       @addtogroup DifferenceEngine
 */
class DiffFormatter {
	/**
	 * Number of leading context "lines" to preserve.
	 *
	 * This should be left at zero for this class, but subclasses
	 * may want to set this to other values.
	 */
	var $leading_context_lines = 0;

	/**
	 * Number of trailing context "lines" to preserve.
	 *
	 * This should be left at zero for this class, but subclasses
	 * may want to set this to other values.
	 */
	var $trailing_context_lines = 0;

	/**
	 * Format a diff.
	 *
	 * @param $diff object
	 *       	 A Diff object.
	 * @return string The formatted output.
	 */
	function format($diff) {

		$xi = $yi = 1;
		$block = false;
		$context = array();

		$nlead = $this->leading_context_lines;
		$ntrail = $this->trailing_context_lines;

		$this->_start_diff();

		foreach($diff->edits as $edit){
			if($edit->type == 'copy'){
				if(is_array($block)){
					if(sizeof($edit->orig) <= $nlead + $ntrail){
						$block[] = $edit;
					}else{
						if($ntrail){
							$context = array_slice($edit->orig, 0, $ntrail);
							$block[] = new _DiffOp_Copy($context);
						}
						$this->_block($x0, $ntrail + $xi - $x0, $y0, $ntrail + $yi - $y0, $block);
						$block = false;
					}
				}
				$context = $edit->orig;
			}else{
				if(! is_array($block)){
					$context = array_slice($context, sizeof($context) - $nlead);
					$x0 = $xi - sizeof($context);
					$y0 = $yi - sizeof($context);
					$block = array();
					if($context)
						$block[] = new _DiffOp_Copy($context);
				}
				$block[] = $edit;
			}

			if($edit->orig)
				$xi += sizeof($edit->orig);
			if($edit->closing)
				$yi += sizeof($edit->closing);
		}

		if(is_array($block))
			$this->_block($x0, $xi - $x0, $y0, $yi - $y0, $block);

		$end = $this->_end_diff();

		return $end;
	}

	function _block($xbeg, $xlen, $ybeg, $ylen, &$edits) {
		$this->_start_block($this->_block_header($xbeg, $xlen, $ybeg, $ylen));
		foreach($edits as $edit){
			if($edit->type == 'copy')
				$this->_context($edit->orig);
			elseif($edit->type == 'add')
				$this->_added($edit->closing);
			elseif($edit->type == 'delete')
				$this->_deleted($edit->orig);
			elseif($edit->type == 'change')
				$this->_changed($edit->orig, $edit->closing);
			else
				trigger_error('Unknown edit type', E_USER_ERROR);
		}
		$this->_end_block();

	}

	function _start_diff() {
		ob_start();
	}

	function _end_diff() {
		$val = ob_get_contents();
		ob_end_clean();
		return $val;
	}

	function _block_header($xbeg, $xlen, $ybeg, $ylen) {
		if($xlen > 1)
			$xbeg .= "," . ($xbeg + $xlen - 1);
		if($ylen > 1)
			$ybeg .= "," . ($ybeg + $ylen - 1);

		return $xbeg . ($xlen ? ($ylen ? 'c' : 'd') : 'a') . $ybeg;
	}

	function _start_block($header) {
		echo $header . "\n";
	}

	function _end_block() {
	}

	function _lines($lines, $prefix = ' ') {
		foreach($lines as $line)
			echo "$prefix $line\n";
	}

	function _context($lines) {
		$this->_lines($lines);
	}

	function _added($lines) {
		$this->_lines($lines, '>');
	}
	function _deleted($lines) {
		$this->_lines($lines, '<');
	}

	function _changed($orig, $closing) {
		$this->_deleted($orig);
		echo "---\n";
		$this->_added($closing);
	}
}

/**
 * A formatter that outputs unified diffs
 * @addtogroup DifferenceEngine
 */

class UnifiedDiffFormatter extends DiffFormatter {
	var $leading_context_lines = 2;
	var $trailing_context_lines = 2;

	function _added($lines) {
		$this->_lines($lines, '+');
	}
	function _deleted($lines) {
		$this->_lines($lines, '-');
	}
	function _changed($orig, $closing) {
		$this->_deleted($orig);
		$this->_added($closing);
	}
	function _block_header($xbeg, $xlen, $ybeg, $ylen) {
		return "@@ -$xbeg,$xlen +$ybeg,$ylen @@";
	}
}

/**
 * A pseudo-formatter that just passes along the Diff::$edits array
 * @addtogroup DifferenceEngine
 */

class ArrayDiffFormatter extends DiffFormatter {
	function format($diff) {
		$oldline = 1;
		$newline = 1;
		$retval = array();
		foreach($diff->edits as $edit)
			switch ($edit->type) {
				case 'add' :
					foreach($edit->closing as $l){
						$retval[] = array(
							'action' => 'add',
							'new' => $l,
							'newline' => $newline ++
						);
					}
					break;
				case 'delete' :
					foreach($edit->orig as $l){
						$retval[] = array(
							'action' => 'delete',
							'old' => $l,
							'oldline' => $oldline ++
						);
					}
					break;
				case 'change' :
					foreach($edit->orig as $i => $l){
						$retval[] = array(
							'action' => 'change',
							'old' => $l,
							'new' => @$edit->closing[$i],
							'oldline' => $oldline ++,
							'newline' => $newline ++
						);
					}
					break;
				case 'copy' :
					$oldline += count($edit->orig);
					$newline += count($edit->orig);
			}
		return $retval;
	}
}

/**
 * Additions by Axel Boldt follow, partly taken from diff.php, phpwiki-1.3.3
 */

define('NBSP', '&#160;'); // iso-8859-x non-breaking space.

/**
 *
 * @todo document
 *       @private
 *       @addtogroup DifferenceEngine
 */
class _HWLDF_WordAccumulator {
	function _HWLDF_WordAccumulator() {
		$this->_lines = array();
		$this->_line = '';
		$this->_group = '';
		$this->_tag = '';
	}

	function _flushGroup($new_tag) {
		if($this->_group !== ''){
			if($this->_tag == 'ins')
				$this->_line .= '<span style="font-weight:bold;color:red;background-color: #99FFFF;">' . 				// '<ins
				                                                                                         // class="diffchange">'
				                                                                                         // .taka
				                                                                                         // be6e
				$this->_group . '</span>';
			elseif($this->_tag == 'del')
				$this->_line .= '<span style="font-weight:bold;color:red;background-color: #99FFFF;">' . 				// '<del
				                                                                                         // class="diffchange">'
				                                                                                         // .
				$this->_group . '</span>';
			else
				$this->_line .= $this->_group;
		}
		$this->_group = '';
		$this->_tag = $new_tag;
	}

	function _flushLine($new_tag) {
		$this->_flushGroup($new_tag);
		if($this->_line != '')
			array_push($this->_lines, $this->_line);
		else
			// make empty lines visible by inserting an NBSP
			array_push($this->_lines, NBSP);
		$this->_line = '';
	}

	function addWords($words, $tag = '') {
		if($tag != $this->_tag)
			$this->_flushGroup($tag);

		foreach($words as $word){
			// new-line should only come as first char of word.
			if($word == '')
				continue;
			if($word[0] == "\n"){
				$this->_flushLine($tag);
				$word = substr($word, 1);
			}
			assert(! strstr($word, "\n"));
			$this->_group .= $word;
		}
	}

	function getLines() {
		$this->_flushLine('~done');
		return $this->_lines;
	}
}

/**
 *
 * @todo document
 *       @private
 *       @addtogroup DifferenceEngine
 */
class WordLevelDiff extends MappedDiff {
	const MAX_LINE_LENGTH = 10000;

	function WordLevelDiff($orig_lines, $closing_lines, $pDiffType = DIFF_TYPE) {
		$this->m_diffType = $pDiffType;
		list($orig_words, $orig_stripped) = $this->_split($orig_lines);
		list($closing_words, $closing_stripped) = $this->_split($closing_lines);
		// var_dump($closing_words, $closing_stripped);
		$this->MappedDiff($orig_words, $closing_words, $orig_stripped, $closing_stripped);

	}

	function _split($lines) {

		$words = array();
		$stripped = array();
		$first = true;
		foreach($lines as $line){
			// If the line is too long, just pretend the entire line is one big
			// word
			// This prevents resource exhaustion problems
			if($first){
				$first = false;
			}else{
				// Dont add new line symbol here. They should be added in the
				// lines themselves otherwise
				// the new line symbols of lines which have not been changed
				// will be lost
				// $words[] = "\n";
				// $stripped[] = "\n";
			}
			if(strlen($line) > self::MAX_LINE_LENGTH){
				$words[] = $line;
				$stripped[] = $line;
			}else{
				$m = array();
				//preg_match_all('/ (?: (?!< \n) [^\S\n])?( [^\S\n]+ | [0-9_A-Za-z\x80-\xff]+ | . )  /xs', $line, $m)
				$lCount = preg_match_all('/ (?: (?!< \n) [^\S\n])?( [^\S\n]+ | [0-9_A-Za-z\x80-\xff]+ | . ) /xs', $line, $m);
				if($lCount > 0){
// 					for($i = 0; $i < $lCount; ++$i){
// 						if($m[1][$i] != ''){
// 							$words[] = $m[1][$i];
// 							$stripped[] = $m[1][$i];
// 						}
// 						$words[] = $m[2][$i];
// 						$stripped[] = $m[2][$i];
// 					}
					$words = array_merge($words, $m[0]);
					$stripped = array_merge($stripped, $m[0]);
				}
			}
		}
// 		var_dump($words, $stripped);
		return array(
			$words,
			$stripped
		);
	}

	function orig() {
		$orig = new _HWLDF_WordAccumulator();

		foreach($this->edits as $edit){
			if($edit->type == 'copy')
				$orig->addWords($edit->orig);
			elseif($edit->orig)
				$orig->addWords($edit->orig, 'del');
		}
		$lines = $orig->getLines();

		return $lines;
	}

	function closing() {
		$closing = new _HWLDF_WordAccumulator();

		foreach($this->edits as $edit){
			if($edit->type == 'copy')
				$closing->addWords($edit->closing);
			elseif($edit->closing)
				$closing->addWords($edit->closing, 'ins');
		}
		$lines = $closing->getLines();

		return $lines;
	}
}

function str_split_unicode($str, $l = 0) {
	if ($l > 0) {
		$ret = array();
		$len = mb_strlen($str, "UTF-8");
		for ($i = 0; $i < $len; $i += $l) {
			$ret[] = mb_substr($str, $i, $l, "UTF-8");
		}
		return $ret;
	}
	return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
}

/**
 *
 * @todo document
 *       @private
 *       @addtogroup DifferenceEngine
 */
class CharLevelDiff extends MappedDiff {
	function CharLevelDiff($orig_lines, $closing_lines, $pDiffType = DIFF_TYPE) {
		$this->m_diffType = $pDiffType;
		list($orig_words, $orig_stripped) = $this->_split($orig_lines);
		list($closing_words, $closing_stripped) = $this->_split($closing_lines);
		// var_dump($closing_words, $closing_stripped);
		$this->MappedDiff($orig_words, $closing_words, $orig_stripped, $closing_stripped);

	}

	function _split($lines) {

		$words = array();
		$stripped = array();
		$first = true;
		foreach($lines as $line){
			// If the line is too long, just pretend the entire line is one big
			// word
			// This prevents resource exhaustion problems
// 			$words = array_merge($words, str_split($line));
// 			$stripped = array_merge($stripped, str_split($line));
			$words = array_merge($words, str_split_unicode($line, 1));
			$stripped = array_merge($stripped, str_split_unicode($line, 1));
// 				$m = array();
// 				if(preg_match_all('/ ( [^\S\n]+ | [0-9_A-Za-z\x80-\xff]+ | . ) (?: (?!< \n) [^\S\n])? /xs', $line, $m)){
// 					$words = array_merge($words, $m[0]);
// 					$stripped = array_merge($stripped, $m[1]);
// 				}
// 			}
		}
// 		var_dump($lines);
// 		var_dump($words);

		return array(
			$words,
			$stripped
		);
	}

	function orig() {
		$orig = new _HWLDF_WordAccumulator();

		foreach($this->edits as $edit){
			if($edit->type == 'copy')
				$orig->addWords($edit->orig);
			elseif($edit->orig)
			$orig->addWords($edit->orig, 'del');
		}
		$lines = $orig->getLines();

		return $lines;
	}

	function closing() {
		$closing = new _HWLDF_WordAccumulator();

		foreach($this->edits as $edit){
			if($edit->type == 'copy')
				$closing->addWords($edit->closing);
			elseif($edit->closing)
			$closing->addWords($edit->closing, 'ins');
		}
		$lines = $closing->getLines();

		return $lines;
	}
}

class ObjectDiffPatchGenerator extends DiffFormatter {
	var $contextLineNum = 0;
	var $usecontextbookmark = 0;
	var $uid = 8;
	var $m_DiffType;
	function format($pDiff, $pUid) {
		$this->uid = $pUid;
		$oldline = 1;
		$newline = 1;
		$retval = array(
			"firsttext",
			"secondtext"
		);
		$this->m_DiffType = $pDiff->GetDiffType();
		$this->calculateEditPositions($pDiff->edits);
		$lResult = array();
		// var_dump($pDiff->edits);
		$lResult = $this->processDiffResult($pDiff->edits);

		return $lResult;
	}

	function processDiffResult(&$pDiffEdits, $pDiffIsWordBased = false, $pDiffIsCharBased = false) {
// 		var_dump($pDiffEdits);
// 		global $gDebug;
// 		if($gDebug){
// 			var_dump($pDiffEdits);
// 		}
		$lResult = array();
		foreach($pDiffEdits as $lCurrentEdit){

			if($lCurrentEdit->type == 'copy'){
				// These are no changes so we skip them
				continue;
			}
			switch ($lCurrentEdit->type) {
				case 'add' :
					$lChangeArr = $this->_added($lCurrentEdit->closing);
					$lResult[] = createChange(CHANGE_INSERT_TYPE, $lCurrentEdit->getStartPos(), $lCurrentEdit->getEndPos(), $this->uid, $lChangeArr['secondline']);
					break;
				case 'delete' :
					$lChangeArr = $this->_deleted($lCurrentEdit->orig);
					$lResult[] = createChange(CHANGE_DELETE_TYPE, $lCurrentEdit->getStartPos(), $lCurrentEdit->getEndPos(), $this->uid, $lChangeArr['firstline']);
					break;
				case 'change' :
					if(($this->m_DiffType == DIFF_CHAR_BASED_TYPE && $pDiffIsCharBased) || ($this->m_DiffType == DIFF_WORD_BASED_TYPE && $pDiffIsWordBased)){
						// Generate a delete and insert change
						$lChangeArr = $this->_deleted($lCurrentEdit->orig);
						$lResult[] = createChange(CHANGE_DELETE_TYPE, $lCurrentEdit->getStartPos(), $lCurrentEdit->getEndPos(), $this->uid, $lChangeArr['firstline']);
						$lChangeArr = $this->_added($lCurrentEdit->closing);
						$lResult[] = createChange(CHANGE_INSERT_TYPE, $lCurrentEdit->getEndPos() + 1, $lCurrentEdit->getEndPos() + 1, $this->uid, $lChangeArr['secondline']);
					}elseif(! $pDiffIsWordBased){
						// If the current diff is line based - get the word
						// based diff
// 						var_dump($lCurrentEdit->orig, $lCurrentEdit->closing);
						$lResult = array_merge($lResult, $this->processLineChangeEdit($lCurrentEdit));
					}else{
						$lResult = array_merge($lResult, $this->processWordChangeEdit($lCurrentEdit));
					}
					break;
			}
		}
		// var_dump($pDiffEdits);
		return $lResult;
	}

	/**
	 * Calculate the edit positions according to the original text
	 *
	 * @param $pDiffEdits unknown_type
	 */
	function calculateEditPositions(&$pDiffEdits, $pStartPos = 0) {

		foreach($pDiffEdits as $lIdx => $lCurrentEdit){
			// Mark the start as the character following the previous edit
			if($lIdx == 0){
				$pDiffEdits[$lIdx]->setStartPos($pStartPos);
			}else{
				// If the previous edit has been an insert-> don't add 1
				$lPreviousEditEnd = $pDiffEdits[$lIdx - 1]->getEndPos();

				if($pDiffEdits[$lIdx - 1]->type != 'add'){
					$lPreviousEditEnd ++;
				}
				$pDiffEdits[$lIdx]->setStartPos($lPreviousEditEnd);
			}
			$lChangeArr = $this->_context($lCurrentEdit->orig);
			switch ($lCurrentEdit->type) {
				case 'copy' :
				case 'delete' :
				case 'change' :
					// These operations have length which equals the length of
					// the modified original text
					// var_dump(($lChangeArr['firstline']));
					$pDiffEdits[$lIdx]->setEndPos($pDiffEdits[$lIdx]->getStartPos() + mb_strlen($lChangeArr['firstline']) - 1);
					break;
				case 'add' :
					// Add operations end where they start
					$pDiffEdits[$lIdx]->setEndPos($pDiffEdits[$lIdx]->getStartPos());
					break;
			}
		}
		// var_dump($pDiffEdits);
	}

	function addedLine($line) {
		return $this->wrapLine('', $this->addedcolor, $line);
	}

	// HTML-escape parameter before calling this
	function deletedLine($line) {
		return $this->wrapLine('', $this->deletedcolor, $line);
	}

	// HTML-escape parameter before calling this
	function contextLine($line, $id) {
		return (($this->usecontextbookmark) ? "<div id='$id'></div>" : "") . $line;
	}

	private function wrapLine($marker, $color, $line) {
		if($line !== ''){
			// The <div> wrapper is needed for 'overflow: auto' style to scroll
			// properly
			// ~ $line = "<span style='background-color:
			// $color;width:100%'>$marker $line</span>";
			$line = $marker . $line;
		}
		return $line;
	}

	function emptyLine() {
		return "";
	}

	function _added($lines) {
		$retval = array(
			"firstline",
			"secondline"
		);
		foreach($lines as $line){
			$retval["firstline"] = $retval["firstline"] . $this->emptyLine();
			$retval["secondline"] = $retval["secondline"] . $this->contextLine($line);
		}
		return $retval;
	}

	function _deleted($lines) {
		$retval = array(
			"firstline",
			"secondline"
		);
		foreach($lines as $line){
			$retval["firstline"] = $retval["firstline"] . $this->deletedLine($line);
			$retval["secondline"] = $retval["secondline"] . $this->emptyLine();
		}
		return $retval;
	}

	function _context($lines) {
		$retval = array(
			"firstline",
			"secondline"
		);
		foreach($lines as $line){
			$retval["firstline"] .= $this->contextLine($line, "1_" . $this->contextLineNum);
			// $retval["firstline"] .= $this->contextLine($line, "1_" .
			// $this->contextLineNum) . "\n";
			$retval["secondline"] = $retval["secondline"] . $this->contextLine($line, "2_" . $this->contextLineNum);
			$this->contextLineNum ++;
		}
		return $retval;
	}
	/**
	 *
	 * @param $pChange _DiffOp
	 */
	function processLineChangeEdit($pChange) {

		$lDiff = new WordLevelDiff($pChange->orig, $pChange->closing, $this->m_DiffType);
// 		global $gDebug;
// 		if($gDebug){
// // 			var_dump($lDiff);
// 		}

		$this->calculateEditPositions($lDiff->edits, $pChange->getStartPos());
		// var_dump($lDiff);
		return $this->processDiffResult($lDiff->edits, true);
	}

	function processWordChangeEdit($pChange) {

		$lDiff = new CharLevelDiff($pChange->orig, $pChange->closing, $this->m_DiffType);
		$this->calculateEditPositions($lDiff->edits, $pChange->getStartPos());
		// var_dump($lDiff);
		return $this->processDiffResult($lDiff->edits, false, true);
	}

}


// @formatter:off
/**
 * We assume that a change is an array with the following format
 * (indexes are inclusive and start from 0
 * i.e.
 * a delete from 0 to 10 means deleting the first 11 symbols
 * an insert at pos X means inserting the text BEFORE pos X
 * ):
 * start_idx => The idx in the text where the change occurred
 * end_idx => The idx where the change ended (for delete changes)
 * change_type => The type of the change (Insert/Delete)
 * uid => an array containing the ids of all the users that performed this
 * change
 * modified_text => The text that has been inserted/deleted
 */

// @formatter:on

/**
 * Returns a single array containing all the changes normalized
 *
 * @param $pChanges1Arr array
 * @param $pChanges2Arr array
 */
function NormalizeChanges($pChanges1Arr, $pChanges2Arr) {
	if(! is_array($pChanges2Arr)){
		if(is_array($pChanges1Arr)){
			return $pChanges1Arr;
		}
		return array();
	}
	if(! is_array($pChanges1Arr)){
		if(is_array($pChanges2Arr)){
			return $pChanges2Arr;
		}
		return array();
	}
	$lResult = array();
	AllChangesLoop:
// 	var_dump('Changes1', $pChanges1Arr);
// 	var_dump('Changes2', $pChanges2Arr);
	while(count($pChanges1Arr) > 0 && count($pChanges2Arr) > 0){
		$lIdxChange1 = $pChanges1Arr[0]['start_idx'];
		$lIdxChange2 = $pChanges2Arr[0]['start_idx'];
		$lOtherChangeArr = &$pChanges2Arr;
		if($lIdxChange1 < $lIdxChange2){
			$lCurrentChange = array_shift($pChanges1Arr);
		}else{
			$lOtherChangeArr = &$pChanges1Arr;
			$lCurrentChange = array_shift($pChanges2Arr);
		}

		if($lCurrentChange['change_type'] == (int) CHANGE_INSERT_TYPE){
			$lResult[] = $lCurrentChange;
			continue;
		}
		// var_dump($lOtherChangeArr);
		// Process changes from the other array which overlap with the current
		// change
		OtherChangesLoop:
		while(count($lOtherChangeArr) > 0 && $lOtherChangeArr[0]['start_idx'] <= $lCurrentChange['end_idx']){
			$lOtherChange = array_shift($lOtherChangeArr);
			if($lOtherChange['change_type'] == (int) CHANGE_INSERT_TYPE){
				if($lOtherChange['start_idx'] > $lCurrentChange['start_idx']){
					// Split the current change in 2 parts - one before and one
					// after the insert
					$lFirstPartChange = array(
						'start_idx' => $lCurrentChange['start_idx'],
						'change_type' => $lCurrentChange['change_type'],
						'end_idx' => $lOtherChange['start_idx'] - 1,
						'uid' => $lCurrentChange['uid'],
						'modified_text' => mb_substr($lCurrentChange['modified_text'], 0, $lOtherChange['start_idx'] - $lCurrentChange['start_idx'])
					);
					$lResult[] = $lFirstPartChange;
					$lCurrentChange['modified_text'] = mb_substr($lCurrentChange['modified_text'], $lOtherChange['start_idx'] - $lCurrentChange['start_idx']);
					$lCurrentChange['start_idx'] = $lOtherChange['start_idx'];
				}
				$lResult[] = $lOtherChange;
				continue;
			}else{
				if($lOtherChange['start_idx'] > $lCurrentChange['start_idx']){
					// Process the part of the current change which is before
					// the other change
					$lFirstPartChange = array(
						'start_idx' => $lCurrentChange['start_idx'],
						'change_type' => $lCurrentChange['change_type'],
						'end_idx' => $lOtherChange['start_idx'] - 1,
						'uid' => $lCurrentChange['uid'],
						'modified_text' => mb_substr($lCurrentChange['modified_text'], 0, $lOtherChange['start_idx'] - $lCurrentChange['start_idx'])
					);
					$lResult[] = $lFirstPartChange;
					$lCurrentChange['modified_text'] = mb_substr($lCurrentChange['modified_text'], $lOtherChange['start_idx'] - $lCurrentChange['start_idx']);
					$lCurrentChange['start_idx'] = $lOtherChange['start_idx'];
				}
				if($lOtherChange['end_idx'] < $lCurrentChange['end_idx']){
					// Process the other change (include the users of the
					// current change in the other change)
					$lOtherChange['uid'] = array_merge($lOtherChange['uid'], $lCurrentChange['uid']);
					$lResult[] = $lOtherChange;
					if($lCurrentChange['end_idx'] > $lOtherChange['end_idx']){
						// If the current change is not over -> continue with
						// other possible changes
						$lCurrentChange['modified_text'] = mb_substr($lCurrentChange['modified_text'], $lOtherChange['start_idx'] - $lCurrentChange['start_idx']);
						$lCurrentChange['start_idx'] = $lOtherChange['end_idx'] + 1;
						continue;
					}else{
						// If the current change is over - go to the next change
						// break 2;
						goto AllChangesLoop;
					}
				}else{
					// Process the current change
					$lCurrentChange['uid'] = array_merge($lOtherChange['uid'], $lCurrentChange['uid']);
					$lResult[] = $lCurrentChange;
					// If the other change is not over - we will insert it again
					// in the other array
					if($lOtherChange['end_idx'] > $lCurrentChange['end_idx']){
						$lOtherChange['modified_text'] = mb_substr($lOtherChange['modified_text'], $lCurrentChange['end_idx'] - $lOtherChange['start_idx'] + 1);
						$lOtherChange['start_idx'] = $lCurrentChange['end_idx'] + 1;
						array_unshift($lOtherChangeArr, $lOtherChange);
					}
					// Continue with the next change
					// break 2;
					goto AllChangesLoop;
				}

			}
		}
		// Process the current change
		$lResult[] = $lCurrentChange;
	}
	if(is_array($pChanges1Arr)){
		$lResult = array_merge($lResult, $pChanges1Arr);
	}
	if(is_array($pChanges2Arr)){
		$lResult = array_merge($lResult, $pChanges2Arr);
	}
	return $lResult;
}

function ProcessChanges($pOriginalContent, $pChanges1Arr, $pChanges2Arr = false, $pModifyBrs = false) {
	/**
	 * That is important here cause otherways after the nl2br in the end we will
	 * 2 times more new lines.(It is safe cause html considers nl to be just another whitespace)
	 * @var unknown_type
	 */
	$pOriginalContent = preg_replace('/\n/', ' ', $pOriginalContent);
	if($pModifyBrs){
		$pOriginalContent = br2nl_custom($pOriginalContent);
	}
// 	error_reporting(-1);
	$pOriginalContent = CustomHtmlEntitiesDecode($pOriginalContent);
	$lNormalizedChanges = NormalizeChanges($pChanges1Arr, $pChanges2Arr);
// 	var_dump(strip_tags($pOriginalContent));
// 	var_dump(mb_substr(strip_tags($pOriginalContent), 0, 1553));
	// exit;
	$lDom = new DOMDocument('1.0', 'utf-8');
// 	error_reporting(-1);
// 	var_dump($pOriginalContent);
	$lFakeRootNode = null;
	if(! $lDom->loadXML($pOriginalContent)){
		//Try to append a fake root
		$lFakeRootNode = $lDom->appendChild($lDom->createElement(FAKE_ROOT_NODE_NAME));
		$lFragment = $lDom->createDocumentFragment();
		if($lFragment->appendXML($pOriginalContent)){
			$lFakeRootNode->appendChild($lFragment);
		}else{
			return ProcessChangesTxt($pOriginalContent, $lNormalizedChanges);
		}
	}
	$lCurrentOffset = 0;
	$lCurrentNode = $lDom->documentElement;
	$lCurrentNode = GetFirstTextNodeDescendant($lDom->documentElement);

	$lCurrentPos = 0;
	$lChangeIdx = 0;
	while(count($lNormalizedChanges) > 0 && $lCurrentNode){
		$lCurrentChange = array_shift($lNormalizedChanges);
		$lChangeIdx++;
// 		$lTemp = '';
		$lChangeClass = GetChangeClass($lCurrentChange);
		while($lCurrentNode && ($lCurrentPos + mb_strlen($lCurrentNode->nodeValue)) <= $lCurrentChange['start_idx']){
// 			var_dump($lCurrentNode->nodeValue);
// 			$lTemp .= $lCurrentNode->nodeValue;
			$lCurrentPos += mb_strlen($lCurrentNode->nodeValue);
			$lCurrentNode = GetNextTextNode($lCurrentNode);
		}
// 		var_dump(1);
// 		var_dump($lTemp);
// 		var_dump($lCurrentPos);

		// If there are no more text nodes - we mark the current change as
		// unprocessed and move forward
		if(! $lCurrentNode){
			array_unshift($lNormalizedChanges, $lCurrentChange);
			break;
		}
		// Now we are sure that the following change begins in the current text
		// node
		// We split the current node in 2 parts - the part before the change and
		// the part containing the start of the change
		if($lCurrentPos < $lCurrentChange['start_idx']){
			$lTextContent = $lCurrentNode->nodeValue;
			$lBeforeLength = $lCurrentChange['start_idx'] - $lCurrentPos;
			$lTextBefore = mb_substr($lTextContent, 0, $lBeforeLength);
			$lTextAfter = mb_substr($lTextContent, $lBeforeLength);

			$lBeforeNode = $lCurrentNode->parentNode->insertBefore($lDom->createTextNode($lTextBefore), $lCurrentNode);

			$lOldNode = $lCurrentNode;
			$lCurrentNode = $lDom->createTextNode($lTextAfter);
			$lOldNode->parentNode->replaceChild($lCurrentNode, $lOldNode);
			$lCurrentPos = $lCurrentChange['start_idx'];
		}
		// Now we are sure that the current change begins at the start of the
		// current node
		if($lCurrentChange['change_type'] == CHANGE_INSERT_TYPE){
			// If the change is insert change - just insert the content before
			// the current node and continue with the following changes
			$lCurrentChangeNode = $lCurrentNode->parentNode->insertBefore($lDom->createElement(CHANGE_INSERT_NODE_NAME, $lCurrentChange['modified_text']), $lCurrentNode);
			SetChangeNodeAttributes($lCurrentChangeNode, $lCurrentChange, $lChangeIdx);
// 			$lCurrentChangeNode->setAttribute('class', $lChangeClass);
			continue;
		}

		// Now we are sure that the current change is of type delete and that it
		// starts at the start of the current node
		$lChangeLength = $lCurrentChange['end_idx'] - $lCurrentChange['start_idx'];

		while($lCurrentNode && $lCurrentPos + mb_strlen($lCurrentNode->nodeValue) <= $lCurrentChange['end_idx'] + 1){
			$lOldNode = $lCurrentNode;
			$lCurrentNode = $lDom->createElement('delete', $lCurrentNode->nodeValue);
// 			$lCurrentNode->setAttribute('class', $lChangeClass);
// 			var_dump($lCurrentNode);
			SetChangeNodeAttributes($lCurrentNode, $lCurrentChange, $lChangeIdx);
			$lOldNode->parentNode->replaceChild($lCurrentNode, $lOldNode);
			$lCurrentPos += mb_strlen($lCurrentNode->nodeValue);
			$lCurrentNode = GetNextTextNode($lCurrentNode);
		}

		// If there are no more text nodes - skip the remaining of the change
		if(! $lCurrentNode){
			break;
		}
		// If the whole change has been processed - continue with the following
		// changes
		if($lCurrentPos == $lCurrentChange['end_idx'] + 1){
			continue;
		}
		$lChangeIdx++;

		// var_dump($lCurrentNode->parentNode);
		// Now we have to split the node in 2 - the part which contains the
		// change and the other after it
		$lTextContent = $lCurrentNode->nodeValue;
		// var_dump($lTextContent, $lCurrentPos);
		$lBeforeLength = $lCurrentChange['end_idx'] - $lCurrentPos + 1;

		// if($lCurrentChange['start_idx'] == 413){
		// var_dump($lBeforeLength);
		// }
		$lTextBefore = mb_substr($lTextContent, 0, $lBeforeLength);
		$lTextAfter = mb_substr($lTextContent, $lBeforeLength);
		$lBeforeNode = $lCurrentNode->parentNode->insertBefore($lDom->createElement('delete', $lTextBefore), $lCurrentNode);
// 		$lBeforeNode->setAttribute('class', $lChangeClass);
		SetChangeNodeAttributes($lBeforeNode, $lCurrentChange, $lChangeIdx);
		$lOldNode = $lCurrentNode;
		$lCurrentNode = $lDom->createTextNode($lTextAfter);
		$lOldNode->parentNode->replaceChild($lCurrentNode, $lOldNode);
		$lCurrentPos = $lCurrentChange['end_idx'] + 1;

	}
	// var_dump($lNormalizedChanges);
	// exit;

	// If there are no text nodes - we ignore the following delete changes and
	// insert everything to the end
	while(count($lNormalizedChanges) > 0){
		$lCurrentChange = array_shift($lNormalizedChanges);
		$lChangeClass = GetChangeClass($lCurrentChange);
		if($lCurrentChange['change_type'] == CHANGE_DELETE_TYPE){
			continue;
		}
		$lChangeIdx++;
		$lCurrentChangeNode = $lDom->documentElement->appendChild($lDom->createElement('insert', $lCurrentChange['modified_text']));
		SetChangeNodeAttributes($lCurrentChangeNode, $lCurrentChange, $lChangeIdx);		
	}
	$lResult = '';
	if($lFakeRootNode){
		foreach ($lFakeRootNode->childNodes as $lChild) {
			$lResult .= $lDom->saveXML($lChild);
		}
	}else{
		$lResult = $lDom->saveXML($lDom->documentElement);
	}

	if($pModifyBrs){
		$lResult = nl2br_custom($lResult);
	}
	return $lResult;
}

function SetChangeNodeAttributes(&$pChangeNode, $pChange, $pChangeIdx){
	$lChangeClass = GetChangeClass($pChange);
	$lUserIds = '';
	$lUserNames = '';
	$lFirst = true;
// 	var_dump($pChange['uid']);
	foreach ($pChange['uid'] as $lUserData){
		$lUserId = $lUserData['id'];
		$lUserName = $lUserData['name'];
		if($lFirst){
			$lFirst = false;
		}else{
			$lUserIds .= ', ';
			$lUserNames .= ', ';
		}
		$lUserIds .= $lUserId;
		$lUserNames .= $lUserName;
	}
// 	$lTitle = getstr('pjs.version_change_' . $pChange['change_type']);
// 	$lTitle = str_replace('{user_names}', $lUserNames, $lTitle);
	$pChangeNode->setAttribute('ContentEditable', 'false');
	$pChangeNode->setAttribute('class', $lChangeClass);
	$pChangeNode->setAttribute(CHANGE_ID_ATTRIBUTE_NAME, $pChangeIdx);
	$pChangeNode->setAttribute(CHANGE_USER_ID_ATTRIBUTE_NAME, $lUserIds);
// 	$pChangeNode->setAttribute(CHANGE_USER_NAME_ATTRIBUTE_NAME, $lUserNames);
// 	$pChangeNode->setAttribute(CHANGE_TITLE_ATTRIBUTE_NAME, $lTitle);
// 	$pChangeNode->setAttribute(CHANGE_IS_ACCEPTED_ATTRIBUTE_NAME, (int)$pChange['is_accepted']);


}

/**
 * Returns the first text node which is a child of the passed node or false if
 * there is no such node
 * If the node is a text node itself - it will be returned
 *
 * @param $pNode DomNode
 */
function GetFirstTextNodeDescendant(&$pNode) {
	if($pNode->nodeType == 3){
		return $pNode;
	}
	for($i = 0; $i < $pNode->childNodes->length; ++ $i){
		$lChild = $pNode->childNodes->item($i);
		if($lChild->nodeType == 3){
			return $lChild;
		}
		if($lChild->nodeType == 1){
			$lChildFirstTextNode = GetFirstTextNodeDescendant($lChild);
			if($lChildFirstTextNode !== false){
				return $lChildFirstTextNode;
			}
		}
	}
	return false;
}

/**
 * Returns the first text node which is following the specified node or false if
 * there is no such node
 *
 * @param $pNode DomNode
 */
function GetNextTextNode(&$pNode) {
	$lNextSibling = false;
	$lParent = $pNode;
	while($lParent){
		$lNextSibling = $lParent->nextSibling;
		while($lNextSibling){
			if($lNextSibling->nodeType == 3)
				return $lNextSibling;
			if($lNextSibling->nodeType == 1){
				$lTextNode = GetFirstTextNodeDescendant($lNextSibling);
				if($lTextNode)
					return $lTextNode;
			}
			$lNextSibling = $lNextSibling->nextSibling;
		}
		$lParent = $lParent->parentNode;
	}
	return false;
}

function ProcessChangesTxt($pOriginalTxt, $pChangesArr) {
	while(count($pChangesArr) > 0){
		$lCurrentChange = array_pop($pChangesArr);

		// var_dump($lCurrentChange);
		$lTxtToInsert = GetChangeTxtPresentation($lCurrentChange);
		switch ($lCurrentChange['change_type']) {
			case CHANGE_DELETE_TYPE :
				$pOriginalTxt = mb_substr($pOriginalTxt, 0, $lCurrentChange['start_idx']) . $lTxtToInsert . mb_substr($pOriginalTxt, $lCurrentChange['end_idx'] + 1);
				break;
			case CHANGE_INSERT_TYPE :
				$pOriginalTxt = mb_substr($pOriginalTxt, 0, $lCurrentChange['start_idx']) . $lTxtToInsert . mb_substr($pOriginalTxt, $lCurrentChange['start_idx']);
				break;
		}
		// var_dump($pOriginalTxt);
	}
	return $pOriginalTxt;
}

function GetChangeClass($pChange) {
	$lClass = '';
	if($pChange['change_type'] == CHANGE_DELETE_TYPE){
		$lClass .= 'del';
	}else{
		$lClass .= 'ins';
	}
	foreach($pChange['uid'] as $lCurrentUid){
		$lClass .= ' change' . $lCurrentUid['id'];
	}
	if(count($pChange['uid']) > 1){
		$lClass .= ' multiple-change';
	}

	if(!$pChange['is_accepted']){
		$lClass .= ' unacceptedChange';
	}

	return $lClass;
}

function GetChangeTxtPresentation($pChange) {
	$lClass = GetChangeClass($pChange);
	$lUsers = '';
	foreach ($pChange['uid'] as $lIdx => $lCurrentUser) {
		if($lIdx > 0){
			$lUsers .= ', ';
		}
		$lUsers .= $lCurrentUser;
	}
	if($pChange['change_type'] == CHANGE_DELETE_TYPE){
		return '<delete contenteditable="false" usr="' . $lUsers . '" class="' . $lClass . '">' . $pChange['modified_text'] . '</delete>';
	}
	return '<insert contenteditable="false" usr="' . $lUsers . '" class="' . $lClass . '">' . $pChange['modified_text'] . '</insert>';
}

if(! function_exists('br2nl')){
	function br2nl($str) {
		$lResult = preg_replace('/\<br[\s]*\>[\s]*\<\/br\>/', "\n", $str);
// 		$lResult = preg_replace('/\<br[\s\/]*\>/', "\n", $str);
		return $lResult;
	}
}

if(! function_exists('br2nl_custom')){
	function br2nl_custom($str) {
// 		return $str;
// 		var_dump($str);
		$lResult = preg_replace('/\<br[\s]*\>[\s]*\<\/br\>/', "\n", $str);
		$lResult = preg_replace('/\<br[\s\/]*\>/', "\n", $lResult);
		return $lResult;
	}
}

if(! function_exists('nl2br_custom')){
	function nl2br_custom($str) {
		return str_replace("\n", "<br/>", $str);
	}
}

function GetPatch($pOriginalTxt, $pModifiedTxt, $pUid, $pProcessStripped = true, $pDiffType = DIFF_TYPE, $pAddNewLineSymbolsForBr = false, $pInsertBrBeforeParagraphEnd = true) {
	if($pInsertBrBeforeParagraphEnd){
		$pOriginalTxt = preg_replace('/<\/p>/', '<br/></p>', $pOriginalTxt);
		$pModifiedTxt = preg_replace('/<\/p>/', '<br/></p>', $pModifiedTxt);
	}
	
	/**
	 * In html new lines are just whitespace - they are not real new lines
	 * @var unknown_type
	 */
	$pOriginalTxt = preg_replace('/\n/', ' ', $pOriginalTxt);
	$pModifiedTxt = preg_replace('/\n/', ' ', $pModifiedTxt);
	$lOriginalTxt = br2nl_custom($pOriginalTxt);
	$lModifiedTxt = br2nl_custom($pModifiedTxt);



	/*
	 * Here we dont need to double encode the html entities
	 * because we wont try to load the xml. Also when performing the merge
	 * when we calculate the positions we use the text nodes' text values in
	 * which the html special chars are not encoded
	 */
// 	var_dump($pModifiedTxt);
	$lOriginalTxt = CustomHtmlEntitiesDecode($lOriginalTxt, !$pProcessStripped);
	$lModifiedTxt = CustomHtmlEntitiesDecode($lModifiedTxt, !$pProcessStripped);


// 	var_dump($lOriginalTxt);
// 	var_dump(strip_tags($lOriginalTxt), strip_tags($lModifiedTxt));
	if($pProcessStripped){
		$lOriginalLines = explode("\n", strip_tags($lOriginalTxt));
		$lModifiedLines = explode("\n", strip_tags($lModifiedTxt));
	}else{
		$lModifiedLines = explode("\n", ($lModifiedTxt));
		$lOriginalLines = explode("\n", ($lOriginalTxt));
	}
// 	var_dump($lOriginalLines, $lModifiedLines);
	// Add new line to the end of every line except the last one
	// because the new lines have been lost during explode
	if($pAddNewLineSymbolsForBr){
		foreach($lOriginalLines as $lKey => $lLine){
			if($lKey < count($lOriginalLines) - 1){
				$lOriginalLines[$lKey] .= "\n";
			}
		}
		foreach($lModifiedLines as $lKey => $lLine){
			if($lKey < count($lModifiedLines) - 1){
				$lModifiedLines[$lKey] .= "\n";
			}
		}
	}
	$lDiff = new Diff($lOriginalLines, $lModifiedLines, $pDiffType);
	$lPatchGenerator = new ObjectDiffPatchGenerator();
// 	var_dump($lDiff);
	$lPatch = $lPatchGenerator->format($lDiff, $pUid);
// 	var_dump($lPatch);
	return $lPatch;
}

function GetAuthorVersionPatch($pOriginalTxt, $pEditorVersionText, $pEditorUid, $pReveriewerChanges = array(), $pProcessStripped = true) {
	$lEditorVersionChanges = GetPatch($pOriginalTxt, $pEditorVersionText, $pEditorUid);
	$lUnacceptedChanges = GetUnacceptedChanges($pEditorVersionText);
}

function createChange($pChangeType, $pStartIdx, $pEndIdx, $pUid, $pModifiedText, $pIsAccepted = true) {
	if(! is_array($pUid)){
		$pUid = array(array(
			'id' => $pUid
		));
	}

	return array(
		'start_idx' => $pStartIdx,
		'end_idx' => $pEndIdx,
		'uid' => ($pUid),
		'modified_text' => $pModifiedText,
		'change_type' => $pChangeType,
		'is_accepted' => $pIsAccepted
	);
}

/**
 * Here we will fix the order of the changes where it is necessary
 * (i.e. the result of actions I1D1I2 is equivalent to D1I1I2 but
 * we need the correct order of the changes in order to be able to
 * mark correctly the unaccepted changes)
 * @param unknown_type $pPatchChanges - the patch from the editor version with unaccepted changes removed relative to the author version
 * @param unknown_type $pWithDeletesPatchChanges - the patch from the editor version with unaccepted changes not removed relative to the author version
 */
function FixReviewerPatchOrder($pPatchChanges, $pWithDeletesPatchChanges){
	//First find some overlapping changes
	//It is important to note that at this point the
	//delete and insert changes are maximal
	//i.e. there should not occur 2 insert changes at the same
	//symbol or 2 consecutive delete changes
	$lPatchIdx = 0;
	$lWithDelIdx = 0;
	while($lPatchIdx < count($pPatchChanges) - 1 && $lWithDelIdx < count($pWithDeletesPatchChanges)){
		//We look for a delete change immediately followed by an insert change
		if($pPatchChanges[$lPatchIdx]['change_type'] != CHANGE_DELETE_TYPE
			|| $pPatchChanges[$lPatchIdx +1]['change_type'] != CHANGE_INSERT_TYPE
			|| $pPatchChanges[$lPatchIdx +1]['start_idx'] != ($pPatchChanges[$lPatchIdx]['end_idx'] + 1)
			){
			$lPatchIdx++;
			continue;
		}

		$lFinalInsertStartSymbol = $pPatchChanges[$lPatchIdx + 1]['start_idx'];
		$lCurrentDeleteStartSymbol = $pPatchChanges[$lPatchIdx]['start_idx'];
		//We navigate to the first overlapping change in the WithDeletesPatch
		while($pWithDeletesPatchChanges[$lWithDelIdx]['end_idx'] < $pPatchChanges[$lPatchIdx]['start_idx']){
			$lWithDelIdx++;
		}


		while($pWithDeletesPatchChanges[$lWithDelIdx]['start_idx'] < $lFinalInsertStartSymbol){
// 			var_dump($pPatchChanges);

			if($pWithDeletesPatchChanges[$lWithDelIdx]['change_type'] == CHANGE_DELETE_TYPE){
				$lTempEndIdx = $pWithDeletesPatchChanges[$lWithDelIdx]['end_idx'];
// 				var_dump($pPatchChanges[$lPatchIdx]);
				if($lTempEndIdx >= $pPatchChanges[$lPatchIdx]['end_idx']){
					//There is no need to split the delete change - continue with the next pair of delete/insert changes
					break;
				}
				//Split the delete change in 2 parts one before and 1 after
				$lBeforeTxt = mb_substr($pPatchChanges[$lPatchIdx]['modified_text'], $lTempEndIdx - $lCurrentDeleteStartSymbol);
				$lAfterTxt =  mb_substr($pPatchChanges[$lPatchIdx]['modified_text'], $lTempEndIdx - $lCurrentDeleteStartSymbol + 1);
				$lBeforeChange = createChange(CHANGE_DELETE_TYPE, $lCurrentDeleteStartSymbol, $lTempEndIdx,  $pPatchChanges[$lPatchIdx]['uid'], $lBeforeTxt);
				array_splice($pPatchChanges, $lPatchIdx, 0, array(
					$lBeforeChange
				));
				$lPatchIdx ++;
				$lCurrentDeleteStartSymbol = $lTempEndIdx + 1;
				$pPatchChanges[$lPatchIdx]['start_idx'] = $lCurrentDeleteStartSymbol;
				$pPatchChanges[$lPatchIdx]['modified_text'] = $lAfterTxt;

			}else{
				//Split the delete change in 2 parts
				//And after that split the insert change


				$lInsertIdx = $pWithDeletesPatchChanges[$lWithDelIdx]['start_idx'];
				//if by any chance the following insert change in the patch
				//doesnt start with the text in the WithDeletesPatch
				// - continue with the next pair of delete/insert changes
				$lInsertedTxt = $pWithDeletesPatchChanges[$lWithDelIdx]['modified_text'];
				$lInsertedTxtLength = mb_strlen($lInsertedTxt);
				$lInsertChangePatchIdx = $lPatchIdx + 1;
				if(mb_substr($pPatchChanges[$lInsertChangePatchIdx]['modified_text'], 0, $lInsertedTxtLength) != $lInsertedTxt){
					break;
				}

				//First check if we need to add a portion of the delete change
				if($lInsertIdx > $lCurrentDeleteStartSymbol){
					$lBeforeTxt = mb_substr($pPatchChanges[$lPatchIdx]['modified_text'], 0, $lInsertIdx - $lCurrentDeleteStartSymbol);
					$lAfterTxt =  mb_substr($pPatchChanges[$lPatchIdx]['modified_text'], $lInsertIdx - $lCurrentDeleteStartSymbol);

					$lBeforeChange = createChange(CHANGE_DELETE_TYPE, $lCurrentDeleteStartSymbol, $lInsertIdx - 1,  $pPatchChanges[$lPatchIdx]['uid'], $lBeforeTxt);
					array_splice($pPatchChanges, $lPatchIdx, 0, array(
						$lBeforeChange
					));
					$lPatchIdx ++;
					$lInsertChangePatchIdx++;
					$lCurrentDeleteStartSymbol = $lInsertIdx;
					$pPatchChanges[$lPatchIdx]['start_idx'] = $lCurrentDeleteStartSymbol;
					$pPatchChanges[$lPatchIdx]['modified_text'] = $lAfterTxt;

				}
				$lBeforeChange = createChange(CHANGE_INSERT_TYPE, $lInsertIdx, $lInsertIdx,  $pPatchChanges[$lInsertChangePatchIdx]['uid'], $lInsertedTxt);
				array_splice($pPatchChanges, $lPatchIdx, 0, array(
					$lBeforeChange
				));
				$lPatchIdx++;
				$lInsertChangePatchIdx++;
				//Reduce the insert change


				if(mb_strlen($pPatchChanges[$lInsertChangePatchIdx]['modified_text']) == $lInsertedTxtLength){
					//We have placed all the possible inserted characters -
					//remove the change from the patch and
					//continue with the next pair of delete/insert changes
					array_splice($pPatchChanges, $lInsertChangePatchIdx, 1, array(
					));
					break;
				}else{
// 					var_dump($pPatchChanges[$lInsertChangePatchIdx]);
					$lAfterTxt = mb_substr($pPatchChanges[$lInsertChangePatchIdx]['modified_text'], $lInsertedTxtLength);
					$pPatchChanges[$lInsertChangePatchIdx]['modified_text'] = $lAfterTxt;
// 					var_dump($pPatchChanges[$lInsertChangePatchIdx]);
				}

			}
			$lWithDelIdx++;
		}
		$lPatchIdx++;


	}
	return $pPatchChanges;
}

/**
 * Here we will process an overlapping delete and insert patch change
 * and we will try to match the possible unaccapted changes to them
 * @param unknown_type $pPatchChanges - an array containing 2 changes - a delete change and an insert change
 * @param unknown_type $pUnacceptedChanges - an array containing all the possible unaccepted changes which overlap with the passed patch changes
 * @return - return an array with the processed patched changes (the insert
 * and the delete changes may be divided into multiple parts)
 */
function MarkUnacceptedOverlappingChanges($pPatchChanges, $pUnacceptedChanges){
	//We will keep the inserted and delete changes separated
	//So that in the end to be able to return the delete changes before the insert ones
	$lResultIns = array();
	$lResultDel = array();
	if(count($pPatchChanges) != 2){
		return $pPatchChanges;
	}
	$lPatchDeleteChange = $pPatchChanges[0];
	$lPatchInsertChange = $pPatchChanges[1];
	foreach ($pUnacceptedChanges as $lCurrentUnacceptedChange){
		$lIsDeleteChange = false;
		$lChangeToProcess = &$lPatchInsertChange;
		$lResultArr = &$lResultIns;
		if($lCurrentUnacceptedChange['change_type'] == CHANGE_DELETE_TYPE){
			$lChangeToProcess = &$lPatchDeleteChange;
			$lResultArr = &$lResultDel;
			$lIsDeleteChange = true;
		}

		$lPos = mb_strpos($lChangeToProcess['modified_text'], $lCurrentUnacceptedChange['modified_text']);
		if($lPos !== false){
			if($lPos > 0){
				//Before part
				$lBeforeText = mb_substr($lChangeToProcess['modified_text'], 0, $lPos);
				$lEndIdx = $lChangeToProcess['start_idx'];
				if($lIsDeleteChange){
					$lEndIdx = $lChangeToProcess['start_idx'] + $lPos - 1;
				}
				$lBeforeChange = createChange($lCurrentUnacceptedChange['change_type'], $lChangeToProcess['start_idx'], $lEndIdx, $lChangeToProcess['uid'], $lBeforeText);
				$lResultArr[] = $lBeforeChange;
				if($lIsDeleteChange){
					$lChangeToProcess['start_idx'] = $lEndIdx + 1;
				}
				$lChangeToProcess['modified_text'] = mb_substr($lChangeToProcess['modified_text'], $lPos);
			}
			//Unaccepted part
			$lUnacceptedChangeLength = mb_strlen($lCurrentUnacceptedChange['modified_text']);
			$lEndIdx = $lChangeToProcess['start_idx'];
			if($lIsDeleteChange){
				$lEndIdx = $lChangeToProcess['start_idx'] + $lUnacceptedChangeLength - 1;
			}

			$lUnacceptedChange = createChange($lCurrentUnacceptedChange['change_type'], $lChangeToProcess['start_idx'], $lEndIdx, $lCurrentUnacceptedChange['uid'], $lCurrentUnacceptedChange['modified_text'], false);
			$lResultArr[] = $lUnacceptedChange;
			if($lIsDeleteChange){
				$lChangeToProcess['start_idx'] = $lEndIdx + 1;
			}
			//After part - for following processing
			$lChangeToProcess['modified_text'] = mb_substr($lChangeToProcess['modified_text'], $lUnacceptedChangeLength);
		}

	}
	if(mb_strlen($lPatchDeleteChange['modified_text'])){
		$lResultDel[] = $lPatchDeleteChange;
	}
	if(mb_strlen($lPatchInsertChange['modified_text'])){
		$lResultIns[] = $lPatchInsertChange;
	}
	return array_merge($lResultDel, $lResultIns);
}

/**
 * Here we will mark the unaccepted reviewer changes
 * in the patch of the editor changes
 *
 * @param $pUnacceptedChanges unknown_type
 * @param $pPatchChanges unknown_type
 */
function MarkUnacceptedChangesToPatchChanges($pUnacceptedChanges, $pPatchChanges) {
	$lCurrentUnacceptedChangeIdx = 0;
	$lCurrentPatchChangeIdx = 0;
	$lUnacceptedCount = count($pUnacceptedChanges);
	$lPatchCount = count($pPatchChanges);

// 	var_dump($pUnacceptedChanges,$pPatchChanges );
// 	var_dump('Before Patch', $pPatchChanges);
// 	var_dump('Before', $pUnacceptedChanges);
	while($lCurrentUnacceptedChangeIdx < $lUnacceptedCount && $lCurrentPatchChangeIdx < $lPatchCount){
		if($pPatchChanges[$lCurrentPatchChangeIdx]['start_idx'] > $pUnacceptedChanges[$lCurrentUnacceptedChangeIdx]['start_idx']){
			// The patch change starts after the current unaccepted change -
			// skip the unaccepted change
			$lCurrentUnacceptedChangeIdx ++;
			continue;
		}
		if($pPatchChanges[$lCurrentPatchChangeIdx]['change_type'] == CHANGE_INSERT_TYPE){
			$lDiffLength = min($pUnacceptedChanges[$lCurrentUnacceptedChangeIdx]['start_idx'] - $pPatchChanges[$lCurrentPatchChangeIdx]['start_idx'], mb_strlen($pPatchChanges[$lCurrentPatchChangeIdx]['modified_text']));
			if($lDiffLength > 0){
				if($lDiffLength < mb_strlen($pPatchChanges[$lCurrentPatchChangeIdx]['modified_text'])){
					// If only part of the patch change is before the unaccepted
					// change - split the patch change in 2
					// Correct the positions of the following unconfirmed
					// changes and proceed with the part of
					// of the change which overlaps with the unconfirmed change
					$lBeforeText = mb_substr($pPatchChanges[$lCurrentPatchChangeIdx]['modified_text'], 0, $lDiffLength);
					$lAfterText = mb_substr($pPatchChanges[$lCurrentPatchChangeIdx]['modified_text'], $lDiffLength);
					$lBeforeChange = createChange($pPatchChanges[$lCurrentPatchChangeIdx]['change_type'], $pPatchChanges[$lCurrentPatchChangeIdx]['start_idx'], $pPatchChanges[$lCurrentPatchChangeIdx]['end_idx'], $pPatchChanges[$lCurrentPatchChangeIdx]['uid'], $lBeforeText);
					$lAfterChange = createChange($pPatchChanges[$lCurrentPatchChangeIdx]['change_type'], $pPatchChanges[$lCurrentPatchChangeIdx]['start_idx'], $pPatchChanges[$lCurrentPatchChangeIdx]['end_idx'], $pPatchChanges[$lCurrentPatchChangeIdx]['uid'], $lAfterText);
					array_splice($pPatchChanges, $lCurrentPatchChangeIdx, 1, array(
						$lBeforeChange,
						$lAfterChange
					));
					$lPatchCount ++;
				}
// 				var_dump('Ins ' . $lDiffLength . ' ' . var_export($pPatchChanges[$lCurrentPatchChangeIdx], 1) . ' ' . var_export($pUnacceptedChanges[$lCurrentUnacceptedChangeIdx], 1));
				$lCurrentPatchChangeIdx ++;

				for($j = $lCurrentUnacceptedChangeIdx; $j < $lUnacceptedCount; ++ $j){
					$pUnacceptedChanges[$j]['start_idx'] -= $lDiffLength;
					$pUnacceptedChanges[$j]['end_idx'] -= $lDiffLength;
				}
				continue;
			}
			// The 2 changes start at the same char
			if($pPatchChanges[$lCurrentPatchChangeIdx]['change_type'] == $pUnacceptedChanges[$lCurrentUnacceptedChangeIdx]['change_type']){
				// If they are of the same type(both should be insert)

				$lUnaccChangeTxt = $pUnacceptedChanges[$lCurrentUnacceptedChangeIdx]['modified_text'];
				$lPatchChangeTxt = $pPatchChanges[$lCurrentPatchChangeIdx]['modified_text'];

				if(mb_substr($lPatchChangeTxt, 0, mb_strlen($lUnaccChangeTxt)) == $lUnaccChangeTxt){
					// If the current change starts with the unaccepted change
					// If the patch change is longer - divide it in 2 parts
					$lAfterText = mb_substr($lPatchChangeTxt, mb_strlen($lUnaccChangeTxt));
					$lHasAfterChange = false;
					if(mb_strlen($lAfterText) > 0){
						$lHasAfterChange = true;
						$lAfterChange = createChange($pPatchChanges[$lCurrentPatchChangeIdx]['change_type'], $pPatchChanges[$lCurrentPatchChangeIdx]['start_idx'], $pPatchChanges[$lCurrentPatchChangeIdx]['end_idx'], $pPatchChanges[$lCurrentPatchChangeIdx]['uid'], $lAfterText);
					}

					$pPatchChanges[$lCurrentPatchChangeIdx]['is_accepted'] = false;
					$pPatchChanges[$lCurrentPatchChangeIdx]['uid'] = $pUnacceptedChanges[$lCurrentUnacceptedChangeIdx]['uid'];
					$pPatchChanges[$lCurrentPatchChangeIdx]['modified_text'] = $lUnaccChangeTxt;
					if($lHasAfterChange){
						array_splice($pPatchChanges, $lCurrentPatchChangeIdx + 1, 0, array(
							$lAfterChange
						));
						$lPatchCount ++;
					}
					$lCurrentUnacceptedChangeIdx ++;
					$lCurrentPatchChangeIdx ++;
					continue;
				}else{
					// There has been some kind of error in the calculations
					// We should increase the positions of all the following
					// unaccepted changes and continue
					$lUnaccChangeLength = mb_strlen($pUnacceptedChanges[$lCurrentUnacceptedChangeIdx]['modified_text']);
					for($j = $lCurrentUnacceptedChangeIdx + 1; $j < $lUnacceptedCount; ++ $j){
						$pUnacceptedChanges[$j]['start_idx'] -= $lUnaccChangeLength;
						$pUnacceptedChanges[$j]['end_idx'] -= $lUnaccChangeLength;
					}
					$lCurrentUnacceptedChangeIdx ++;
					continue;
				}
			}else{
				//If the 2 changes are not of the same type - continue with the next unacc change
				$lCurrentUnacceptedChangeIdx ++;
				continue;
			}

		}else{
			//The patch change is of delete type
			/*
			if($pPatchChanges[$lCurrentPatchChangeIdx + 1]['change_type'] == CHANGE_INSERT_TYPE
				&& $pPatchChanges[$lCurrentPatchChangeIdx + 1]['start_idx'] = $pPatchChanges[$lCurrentPatchChangeIdx]['end_idx'] + 1
			){
// 				var_dump(1);
				//We are trying to process overlapping changes
				$lUnacceptedPossibleChanges = array();
				$lMaxAffectedSymbol = $pPatchChanges[$lCurrentPatchChangeIdx + 1]['start_idx'] + mb_strlen($pPatchChanges[$lCurrentPatchChangeIdx + 1]['modified_text']);

				for($j = $lCurrentUnacceptedChangeIdx; $j < $lUnacceptedCount; ++$j){
					if($pUnacceptedChanges[$j]['start_idx'] > $lMaxAffectedSymbol){
						break;
					}
					$lUnacceptedPossibleChanges[] = $pUnacceptedChanges[$j];
				}


				$lNewPatchChanges = MarkUnacceptedOverlappingChanges(array($pPatchChanges[$lCurrentPatchChangeIdx], $pPatchChanges[$lCurrentPatchChangeIdx + 1]), $lUnacceptedPossibleChanges);
				var_dump('Unac', $lUnacceptedPossibleChanges);
				var_dump('Acc', $lNewPatchChanges);
				array_splice($pPatchChanges, $lCurrentPatchChangeIdx, 2,
					$lNewPatchChanges
				);
				//Skip processing the insert change
				$lCurrentPatchChangeIdx += count($lNewPatchChanges);
// 				var_dump($lCurrentPatchChangeIdx, $pPatchChanges);
				$lPatchCount = count($pPatchChanges);

				$lAcceptedInsertedSymbols = mb_strlen($pPatchChanges[$lCurrentPatchChangeIdx + 1]['modified_text']);
				$lAcceptedDeletedSymbols = mb_strlen($pPatchChanges[$lCurrentPatchChangeIdx + 1]['modified_text']);
				foreach($lUnacceptedPossibleChanges as $lCurrentPossibleUnacceptedChange){
					if($lCurrentPossibleUnacceptedChange['change_type'] == CHANGE_DELETE_TYPE){
						$lAcceptedDeletedSymbols -= mb_strlen($lCurrentPossibleUnacceptedChange['modified_text']);
					}else{
						$lAcceptedInsertedSymbols -= mb_strlen($lCurrentPossibleUnacceptedChange['modified_text']);
					}
				}

				$lCurrentUnacceptedChangeIdx += count($lUnacceptedPossibleChanges);
				for($j = $lCurrentUnacceptedChangeIdx; $j < $lUnacceptedCount; ++ $j){
					$pUnacceptedChanges[$j]['start_idx'] += $lAcceptedDeletedSymbols - $lAcceptedInsertedSymbols;
					$pUnacceptedChanges[$j]['end_idx'] += $lAcceptedDeletedSymbols - $lAcceptedInsertedSymbols;
				}
				continue;
			}

			*/
			//If the 2 changes are of different type (unconfirmed ins after confirmed delete) or
			//If the 2 changes dont start at the same symbol - then there is some text after the patch change
			//So we should recalculate the positions of the unaccepted changes and proceed with the following patch change
			if($pPatchChanges[$lCurrentPatchChangeIdx]['change_type'] != $pUnacceptedChanges[$lCurrentUnacceptedChangeIdx]['change_type'] || $pPatchChanges[$lCurrentPatchChangeIdx]['start_idx'] != $pUnacceptedChanges[$lCurrentUnacceptedChangeIdx]['start_idx']){
				//$lPatchChangeLength = $pPatchChanges[$lCurrentPatchChangeIdx]['end_idx'] - $pPatchChanges[$lCurrentPatchChangeIdx]['start_idx'] + 1;
				$lPatchChangeLength = mb_strlen($pPatchChanges[$lCurrentPatchChangeIdx]['modified_text']);

// 				var_dump('Del ' . $lPatchChangeLength . ' ' . var_export($pPatchChanges[$lCurrentPatchChangeIdx], 1) . ' ' . var_export($pUnacceptedChanges[$lCurrentUnacceptedChangeIdx], 1));
				for($j = $lCurrentUnacceptedChangeIdx; $j < $lUnacceptedCount; ++ $j){
					$pUnacceptedChanges[$j]['start_idx'] += $lPatchChangeLength;
					$pUnacceptedChanges[$j]['end_idx'] += $lPatchChangeLength;
				}
				$lCurrentPatchChangeIdx ++;
				continue;
			}
			//Now the 2 changes start at the same symbol
			//We will try to match the unprocessed change in the patch change
			//We wont try to match part of the unprocessed change with part of the patch
			//change because the following change in the patch changes
			//wont be a delete change which starts at the following char (The patch changes have maximum lenght)
			//so it wont be possible the other part of the unprocessed change to be matched to a part in the
			//following patch change
			$lPatchChangeLength = $pPatchChanges[$lCurrentPatchChangeIdx]['end_idx'] - $pPatchChanges[$lCurrentPatchChangeIdx]['start_idx'] + 1;
			$lStrPos = mb_strpos($pPatchChanges[$lCurrentPatchChangeIdx]['modified_text'], $pUnacceptedChanges[$lCurrentUnacceptedChangeIdx]['modified_text']);
			if($lStrPos === false){
				//If the changes dont match - proceed
				$lCurrentPatchChangeIdx ++;
				for($j = $lCurrentUnacceptedChangeIdx; $j < $lUnacceptedCount; ++ $j){
					$pUnacceptedChanges[$j]['start_idx'] += $lPatchChangeLength;
					$pUnacceptedChanges[$j]['end_idx'] += $lPatchChangeLength;
				}
				continue;
			}
			if($lStrPos > 0){
				$lCurrentChange = $pPatchChanges[$lCurrentPatchChangeIdx];
				//Split the current change part in 2 - one before the unacc change and one after it
				$lBeforeTxt = mb_substr($lCurrentChange['modified_text'], 0, $lStrPos);
				$lBeforeChangeEndIdx = $lCurrentChange['start_idx'] + $lStrPos;
				$lBeforeChange = createChange($lCurrentChange['change_type'], $lCurrentChange['start_idx'], $lBeforeChangeEndIdx, $lCurrentChange['uid'], $lBeforeTxt, $lCurrentChange['is_accepted']);
				array_splice($pPatchChanges, $lCurrentPatchChangeIdx, 0, array(
					$lBeforeChange
				));
				$lPatchCount++;
				$lCurrentPatchChangeIdx++;

				//Correct the positions of the other unaccepted changes
				for($j = $lCurrentUnacceptedChangeIdx; $j < $lUnacceptedCount; ++ $j){
					$pUnacceptedChanges[$j]['start_idx'] += $lStrPos;
					$pUnacceptedChanges[$j]['end_idx'] += $lStrPos;
				}
				//Set the correct modified text and start pos of the current change
				$pPatchChanges[$lCurrentPatchChangeIdx]['start_idx'] = $lBeforeChangeEndIdx + 1;
				$pPatchChanges[$lCurrentPatchChangeIdx]['modified_text'] = mb_substr($lCurrentChange['modified_text'], $lStrPos);
			}
			//Now the current patch change starts with the current unaccepted change
			$lCurrentChange = &$pPatchChanges[$lCurrentPatchChangeIdx];
			$lCurrentUnacceptedChangeLength = mb_strlen($pUnacceptedChanges[$lCurrentUnacceptedChangeIdx]['modified_text']);
			$lCurrentPatchLength = mb_strlen($pPatchChanges[$lCurrentPatchChangeIdx]['modified_text']);

			if($lCurrentPatchLength > $lCurrentUnacceptedChangeLength){
				//The current change has to be divided in 2 parts -
				//the unaccepted part and the change following it
				$lAfterTxt = mb_substr($lCurrentChange['modified_text'], $lCurrentUnacceptedChangeLength);
// 				$lAfterChangeEndIdx = $lCurrentChange['start_idx'] + $lCurrentUnacceptedChangeLength;
				$lAfterChangeEndIdx = $lCurrentChange['end_idx'];
				$lAfterChange = createChange($lCurrentChange['change_type'], $lCurrentChange['start_idx'] + $lCurrentUnacceptedChangeLength, $lAfterChangeEndIdx, $lCurrentChange['uid'], $lAfterTxt, $lCurrentChange['is_accepted']);
				array_splice($pPatchChanges, $lCurrentPatchChangeIdx + 1, 0, array(
					$lAfterChange
				));
				$lPatchCount++;
// 				var_dump('ASD' . var_export($lCurrentChange, 1));
// 				var_dump('ASD' . var_export($lAfterChange, 1));

				$lCurrentChange['end_idx'] = $lCurrentChange['start_idx'] + $lCurrentUnacceptedChangeLength;
				$lCurrentChange['modified_text'] = $pUnacceptedChanges[$lCurrentUnacceptedChangeIdx]['modified_text'];
			}
			//Mark the current change as unaccepted and proceed with the following changes
			$lCurrentChange['is_accepted'] = false;
			$lCurrentChange['uid'] = $pUnacceptedChanges[$lCurrentUnacceptedChangeIdx]['uid'];
			$lCurrentUnacceptedChangeIdx++;
			$lCurrentPatchChangeIdx++;
		}
	}

// 	echo "\n\nAfter<br/>";
// 	var_dump($pUnacceptedChanges);
	return $pPatchChanges;
}

//We want the shorter changes to be at the end
function cmpInsChanges($pChangeA, $pChangeB){
	$lLenA = mb_strlen($pChangeA);
	$lLenB = mb_strlen($pChangeB);
	if($lLenA < $lLenB){
		return 1;
	}
	if($lLenA > $lLenB){
		return -1;
	}
	return 0;
}

/**
 * Here we will loop through the patch changes
 * and mark the ones which have been accepted
 * @param unknown_type $pPatchChanges
 * @param unknown_type $pReviewersChanges
 */
function MarkAcceptedChanges($pPatchChanges, $pReviewersChanges){
// 	return $pPatchChanges;
	$lCurrentReviewerChangeIdx = 0;
	$lCurrentPatchChangeIdx = 0;
	$lReviewerCount = count($pReviewersChanges);
	$lPatchCount = count($pPatchChanges);

	while($lCurrentReviewerChangeIdx < count($pReviewersChanges) && $lCurrentPatchChangeIdx < count($pPatchChanges)){
		if($pPatchChanges[$lCurrentPatchChangeIdx]['is_accepted'] == false){
			//We skip the changes which have already been marked as unaccepted
			$lCurrentPatchChangeIdx++;
			continue;
		}
		if($pReviewersChanges[$lCurrentReviewerChangeIdx]['end_idx'] < $pPatchChanges[$lCurrentPatchChangeIdx]['start_idx']){
			//This change has been deleted - proceed with the following reviewer change
			$lCurrentReviewerChangeIdx++;
			continue;
		}

		if($pPatchChanges[$lCurrentPatchChangeIdx]['end_idx'] < $pReviewersChanges[$lCurrentReviewerChangeIdx]['start_idx']){
			//This change has been created by the editor - it is new
			$lCurrentPatchChangeIdx++;
			continue;
		}

		//Now we are sure that the 2 changes overlap
		if($pPatchChanges[$lCurrentPatchChangeIdx]['change_type'] != $pReviewersChanges[$lCurrentReviewerChangeIdx]['change_type']){
			//The 2 changes are not of the same type - proceed with the following reviewer change
			$lCurrentReviewerChangeIdx++;
			continue;
		}

		if($pPatchChanges[$lCurrentPatchChangeIdx]['change_type'] == CHANGE_DELETE_TYPE){
			//Now we are trying to match the 2 changes
			$lOffset = 0;
// 			var_dump(mktime());
// 			var_dump($pPatchChanges[$lCurrentPatchChangeIdx]['modified_text'], $pReviewersChanges[$lCurrentReviewerChangeIdx]['modified_text'], $lOffset);
			$lPos = mb_strpos($pPatchChanges[$lCurrentPatchChangeIdx]['modified_text'], $pReviewersChanges[$lCurrentReviewerChangeIdx]['modified_text'], $lOffset);
			while($lPos !== false){
				if($lPos + $pPatchChanges[$lCurrentPatchChangeIdx]['start_idx'] == $pReviewersChanges[$lCurrentReviewerChangeIdx]['start_idx']){
					//The 2 changes match exactly
					//Now we gotta divide the patch change
					if($lPos > 0){
						//Before Part
						$lCurrentChange = $pPatchChanges[$lCurrentPatchChangeIdx];
						$lBeforeTxt = mb_substr($lCurrentChange['modified_text'], 0, $lPos);
						$lBeforeChangeEndIdx = $lCurrentChange['start_idx'] + $lPos;
						$lBeforeChange = createChange($lCurrentChange['change_type'], $lCurrentChange['start_idx'], $lBeforeChangeEndIdx, $lCurrentChange['uid'], $lBeforeTxt, $lCurrentChange['is_accepted']);
						array_splice($pPatchChanges, $lCurrentPatchChangeIdx, 0, array(
							$lBeforeChange
						));
						$lPatchCount++;
						$lCurrentPatchChangeIdx++;
						$pPatchChanges[$lCurrentPatchChangeIdx]['start_idx'] += $lPos;
						$pPatchChanges[$lCurrentPatchChangeIdx]['modified_text'] = mb_substr($lCurrentChange['modified_text'], $lPos);
					}
					//Now the 2 changes start at the same symbol
					//First check if the patch change is longer and needs to be divided in 2
					$lReviewerChangeLength = mb_strlen($pReviewersChanges[$lCurrentReviewerChangeIdx]['modified_text']);
					if(mb_strlen($pPatchChanges[$lCurrentPatchChangeIdx]['modified_text']) > $lReviewerChangeLength){
						$lCurrentChange = $pPatchChanges[$lCurrentPatchChangeIdx];
						$lAfterTxt = mb_substr($lCurrentChange['modified_text'], $lReviewerChangeLength);
						$lAfterChangeEndIdx = $lCurrentChange['start_idx'] + $lReviewerChangeLength;
						$lAfterChange = createChange($lCurrentChange['change_type'], $lCurrentChange['start_idx'] + $lReviewerChangeLength + 1, $lAfterChangeEndIdx, $lCurrentChange['uid'], $lAfterTxt, $lCurrentChange['is_accepted']);
						array_splice($pPatchChanges, $lCurrentPatchChangeIdx + 1, 0, array(
							$lAfterChange
						));
						$lPatchCount++;
						$pPatchChanges[$lCurrentPatchChangeIdx]['modified_text'] = $pReviewersChanges[$lCurrentReviewerChangeIdx]['modified_text'];
						$pPatchChanges[$lCurrentPatchChangeIdx]['end_idx'] = $pReviewersChanges[$lCurrentReviewerChangeIdx]['end_idx'];
					}
					//Mark the authors of the change and proceed with the next patch and reviewer change
					$pPatchChanges[$lCurrentPatchChangeIdx]['uid'] = $pReviewersChanges[$lCurrentReviewerChangeIdx]['uid'];
					$lCurrentPatchChangeIdx++;
					$lCurrentReviewerChangeIdx++;
					continue 2;
				}else{
					//The 2 changes dont match exactly (their positions dont match)
					//Try to match the reviewer change again 1 symbol after the start of the current match
					$lOffset = $lPos + 1;
					if($lOffset + $pPatchChanges[$lCurrentPatchChangeIdx]['start_idx'] > $pReviewersChanges[$lCurrentReviewerChangeIdx]['start_idx']){
						//Proceed with the next editor change
						$lCurrentReviewerChangeIdx++;
						continue 2;
					}
				}
				$lPos = mb_strpos($pPatchChanges[$lCurrentPatchChangeIdx]['modified_text'], $pReviewersChanges[$lCurrentReviewerChangeIdx]['modified_text'], $lOffset);
			};
			$lCurrentPatchChangeIdx++;
			continue;
		}else{
			//The changes are of insert type and they start at the same symbol
			//(because they overlap and each insert change starts and ends at the same symbol);

			//first we should sort the insert editor changes (which start at the current symbol)
			//in ascending order (relative to their length)
			$lInsertReviewerChanges = array();
			for($i = $lCurrentReviewerChangeIdx; $i < $lReviewerCount; ++$i){
				if($pReviewersChanges[$i]['change_type'] != CHANGE_INSERT_TYPE || $pReviewersChanges[$i]['start_idx'] != $pPatchChanges[$lCurrentPatchChangeIdx]['start_idx']){
					break;
				}
				$lInsertReviewerChanges[] = $pReviewersChanges[$i];
			}

			usort($lInsertReviewerChanges, 'cmpInsChanges');
			//Here it is important not to lose the order of the patch change

			//we will keep the matched changes in order not to process them again if there is another insert change
			//in the patch at the same symbol
			$lMatchedReviewerChanges = array();
			//Here we will keep the parts of the current patch change. When we have
			//processed all the parts - continue with the next patch change
			$lPatchChangeParts = array($pPatchChanges[$lCurrentPatchChangeIdx]);
			for($i = 0; $i < count($lPatchChangeParts); ++$i){
				$lCurrentPatchChange = &$lPatchChangeParts[$i];
				if((int)$lCurrentPatchChange['is_processed']){
					continue;
				}
				for($j = 0; $j < count($lInsertReviewerChanges); ++$j){
					$lCurrentReviewerChange = $lInsertReviewerChanges[$j];

					$lPos = mb_strpos($lCurrentPatchChange['modified_text'], $lCurrentReviewerChange['modified_text']);
					if($lPos !== false){
						//Remove the reviewer change from the changes which
						//we will try to match and mark it as matched
						$lHasBeforePart = false;
						if($lPos > 0){
							$lBeforeTxt = mb_substr($lCurrentPatchChange['modified_text'], 0, $lPos);
							$lBeforeChangeEndIdx = $lCurrentPatchChange['start_idx'];
							$lBeforeChange = createChange($lCurrentPatchChange['change_type'], $lCurrentPatchChange['start_idx'], $lBeforeChangeEndIdx, $lCurrentPatchChange['uid'], $lBeforeTxt, $lCurrentPatchChange['is_accepted']);
							array_splice($lPatchChangeParts, $i, 0, array(
								$lBeforeChange
							));
							$lHasBeforePart = true;
							//i+1 because we have already inserted a new change part before the current change
							$lCurrentPatchChange = &$lPatchChangeParts[$i + 1];
							$lPatchChangeParts[$i + 1]['modified_text'] = mb_substr($lPatchChangeParts[$i + 1]['modified_text'], $lPos);
						}

						$lReviewerChangeLength = mb_strlen($lCurrentReviewerChange['modified_text']);
						if(mb_strlen($lCurrentPatchChange['modified_text']) > $lReviewerChangeLength){
							//There is an after part
							$lAfterTxt = mb_substr($lCurrentPatchChange['modified_text'], $lReviewerChangeLength);
							$lAfterChangeEndIdx = $lCurrentPatchChange['start_idx'];
							$lAfterChange = createChange($lCurrentPatchChange['change_type'], $lCurrentPatchChange['start_idx'], $lAfterChangeEndIdx, $lCurrentPatchChange['uid'], $lAfterTxt, $lCurrentPatchChange['is_accepted']);
							$lCurrentIdx = $lHasBeforePart ? $i + 1 : $i;
							array_splice($lPatchChangeParts, $lCurrentIdx + 1, 0, array(
								$lAfterChange
							));
							$lPatchChangeParts[$lCurrentIdx]['modified_text'] = mb_substr($lPatchChangeParts[$lCurrentIdx]['modified_text'], 0, $lReviewerChangeLength);
						}
						$lCurrentPatchChange['is_processed'] = true;
						$lCurrentPatchChange['uid'] = $lCurrentReviewerChange['uid'];
						if($lHasBeforePart){
							//If we have inserted a new part before the current part we have to decrease i
							$i--;
						}

						//Mark the matched reviewer part
						array_splice($lInsertReviewerChanges, $j, 1);
						$lMatchedReviewerChanges[] = $lCurrentReviewerChange;
						continue 2;
					}
				}
			}
			array_splice($pReviewersChanges, $lCurrentReviewerChangeIdx, count($lInsertReviewerChanges) + count($lMatchedReviewerChanges), array_merge($lMatchedReviewerChanges, $lInsertReviewerChanges));
			//Skip the matched reviewer changes
			$lCurrentReviewerChangeIdx += count($lMatchedReviewerChanges);
			//Proceed with the next patch change
			array_splice($pPatchChanges, $lCurrentPatchChangeIdx, 1, $lPatchChangeParts);
			$lCurrentPatchChangeIdx += count($lPatchChangeParts);
		}
	}
	return $pPatchChanges;
}

function RemoveUnacceptedDeleteChanges($pTxt){
	$pTxt = CustomHtmlEntitiesDecode($pTxt);
	$lDom = new DOMDocument('1.0', 'utf-8');
	$lDom->substituteEntities = true;
	// error_reporting(-1);
// 	var_dump($pTxt);
	$lFakeRootNode = null;
// 	$pTxt = str_replace('&nbsp;', ' ', $pTxt);
	if(! $lDom->loadXML($pTxt)){
		//Try to append a fake root
		$lFakeRootNode = $lDom->appendChild($lDom->createElement(FAKE_ROOT_NODE_NAME));
		$lFragment = $lDom->createDocumentFragment();
// 		error_reporting(-1);
		if($lFragment->appendXML($pTxt)){
			$lFakeRootNode->appendChild($lFragment);
		}else{
// 			var_dump('asd', $pTxt);
			return $pTxt;
		}
	}


	$lXPath = new DOMXPath($lDom);
	$lChangesQuery = '//delete';
	$lChangeNodes = $lXPath->query($lChangesQuery);
	for($i = $lChangeNodes->length - 1; $i >= 0; --$i){
// 		var_dump($lChangeNodes->item($i)->textContent);
		$lNode = $lChangeNodes->item($i);
		$lNode->parentNode->removeChild($lNode);
	}
	$lResult = '';
	if(!$lFakeRootNode){
		$lResult = $lDom->saveXML($lDom->documentElement);
	}else{
		foreach ($lFakeRootNode->childNodes as $lChild) {
			$lResult .= $lDom->saveXML($lChild);
		}
	}

// 	$lResult = html_entity_decode($lResult, ENT_COMPAT, 'utf-8');
// 	var_dump($lResult);
	return $lResult;
}

function GetUnacceptedChanges($pTxt) {
	$pTxt = br2nl_custom($pTxt);
	$pTxt = CustomHtmlEntitiesDecode($pTxt);
	$lResult = array();
	$lDom = new DOMDocument('1.0', 'utf-8');
	// error_reporting(-1);
	if(! $lDom->loadXML($pTxt)){
		//Try to append a fake root
		$lFakeRootNode = $lDom->appendChild($lDom->createElement(FAKE_ROOT_NODE_NAME));
		$lFragment = $lDom->createDocumentFragment();
// 				error_reporting(-1);
		if($lFragment->appendXML($pTxt)){
			$lFakeRootNode->appendChild($lFragment);
		}else{
// 			echo 'AAAA';
			return $lResult;
		}
	}
	$lXPath = new DOMXPath($lDom);
	$lChangesQuery = '//' . CHANGE_INSERT_NODE_NAME . ' | //' . CHANGE_DELETE_NODE_NAME;
	$lChangeNodes = $lXPath->query($lChangesQuery);
	for($i = 0; $i < $lChangeNodes->length; ++ $i){
		$lNode = $lChangeNodes->item($i);
		$lModifiedText = $lNode->textContent;
		$lChangeType = $lNode->nodeName == CHANGE_INSERT_NODE_NAME ? CHANGE_INSERT_TYPE : CHANGE_DELETE_TYPE;
		$lStartIdx = GetNodeTextOffset($lNode);
		$lEndIdx = $lStartIdx;
		if($lChangeType == CHANGE_DELETE_TYPE){
			$lEndIdx += mb_strlen($lModifiedText) - 1;
		}
		// Remove the text of the node so that the following changes' positions
		// wont count it
		if($lNode->nodeName == CHANGE_INSERT_NODE_NAME){
			while($lNode->hasChildNodes()){
				$lNode->removeChild($lNode->firstChild);
			}
		}

		$lUserIds = explode(',', $lNode->GetAttribute(CHANGE_USER_ID_ATTRIBUTE_NAME));
		$lUserNames = explode(',', $lNode->GetAttribute(CHANGE_USER_NAME_ATTRIBUTE_NAME));
		$lUsers = array();
		foreach ($lUserIds as $lIdx => $lUserId){
			$lUsers[] = array(
				'id' => $lUserId,
				'name' => $lUserNames[$lIdx],
			);
		}
		$lChange = createChange($lChangeType, $lStartIdx, $lEndIdx, $lUsers, $lModifiedText, false);
		$lResult[] = $lChange;
	}
	return $lResult;
}

/**
 * Returns the text offset against the beginning of the document
 *
 * @param $pNode DomNode
 */
function GetNodeTextOffset($pNode) {
	$lCurrentPos = 0;
	while($pNode){
		while($pNode->previousSibling){
			$pNode = $pNode->previousSibling;
			if($pNode->nodeType == 1 || $pNode->nodeType == 3){
				$lCurrentPos += mb_strlen($pNode->textContent);
			}
		}
		$pNode = $pNode->parentNode;
	}
	return $lCurrentPos;
}

/**
 * Here we will treat the passed argument as an xml/html and
 * we will try to decode the entities in it (e.g. &nbsp;) but we
 * will try to preserve the xml entities (i.e. &lt; &gt; &amp;) in
 * order not to break the xml.
 * We will do this by double encoding the xml entities and
 * after that performing html entities decode (e.g. &lt; -> &amp;lt; ->&lt;)
 * @param unknown_type $pXml
 */
function CustomHtmlEntitiesDecode($pXml, $pPerformHtmlEntitiesDoubleEncode = true){
	//The order here is important. If amps are last there we will have tripple encode
	$lHtmlEntities = array(
		'&amp;' => '&amp;amp;',
		'&lt;' => '&amp;lt;',
		'&gt;' => '&amp;gt;',
		'&quot;' => '&amp;quot;',
	);
// 	var_dump($pXml);
	if($pPerformHtmlEntitiesDoubleEncode){
		$pXml = str_replace(array_keys($lHtmlEntities), array_values($lHtmlEntities), $pXml);
	}
// 	var_dump($pXml);
	return html_entity_decode($pXml, ENT_COMPAT, 'utf-8');
}
?>