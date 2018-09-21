<?php

class rumus {
	
    private static $atas = '&#8593;';
    private static $kiri = '&#8592;';
    private static $diagonal = '&#8598;';
	
    private $matrix = array();
    private $optimal_alignment = array();
    
	//konstruktor
	public function __construct($match_score, $mismatch_score, $gap_penalty) {
        $this->match_score = $match_score;
        $this->mismatch_score = $mismatch_score;
        $this->gap_penalty = $gap_penalty;
    }

    
		//	Menghitung & Representasi tabel penyelarasan.
     
	 
    public function hitung($seq1, $seq2) {
        $this->init($seq1, $seq2);

        for($i = 1; $i < count($this->matrix); $i++) {
            for($j = 1; $j < count($this->matrix[$i]); $j++) {
                $match_mismatch = ($seq1[$i-1] == $seq2[$j-1]) ? $this->match_score : $this->mismatch_score;
                $match = $this->matrix[$i-1][$j-1]['val'] + $match_mismatch;
                $hgap = $this->matrix[$i-1][$j]['val'] + $this->gap_penalty;
                $vgap = $this->matrix[$i][$j-1]['val'] + $this->gap_penalty;
                $max = max($match, $hgap, $vgap);
                $pointer = self::$diagonal;
                if($max == $hgap) {
                    $pointer = self::$atas;
                } else if($max == $vgap) {
                    $pointer = self::$kiri;
                }

                $this->matrix[$i][$j]['pointer'] = $pointer;
                $this->matrix[$i][$j]['val'] = $max;
            }
        }


        $i = count($this->matrix)-1;
        $j = count($this->matrix[0])-1;

        $this->optimal_alignment['seq1'] = array();
        $this->optimal_alignment['seq2'] = array();
        $this->optimal_alignment['aln'] = array();
        $this->optimal_alignment['score'] = $this->matrix[$i][$j]['val'];

        while($i !== 0 and $j !== 0) {
            $base1 = $seq1[$i-1];
            $base2 = $seq2[$j-1];
            $this->matrix[$i][$j]['track'] = true;
            $pointer = $this->matrix[$i][$j]['pointer'];


            if($pointer === self::$diagonal) {
                $i--;
                $j--;
                $this->optimal_alignment['seq1'][] = $base1;
                $this->optimal_alignment['seq2'][] = $base2;
                $this->optimal_alignment['aln'][] = ($base1 === $base2) ? '|' : ' ';
            } else if($pointer === self::$atas) {
                $i--;
                $this->optimal_alignment['seq1'][] = $base1;
                $this->optimal_alignment['seq2'][] = '-';
                $this->optimal_alignment['aln'][] = ' ';
            } else if($pointer === self::$kiri) {
                $j--;
                $this->optimal_alignment['seq1'][] = '-';
                $this->optimal_alignment['seq2'][] = $base2;
                $this->optimal_alignment['aln'][] = ' ';
            } else {
                die("Invalid pointer: $i,$j");
            }
        }

        foreach(array('seq1', 'seq2', 'aln') as $k) {
            $this->optimal_alignment[$k] = array_reverse($this->optimal_alignment[$k]);
        }

        return $this->matrix;
    }

    /**
     * Returns the optimal alignment data structure
     */
    public function getOptimalGlobalAlignment() {
        return $this->optimal_alignment;
    }

    /**
     * Computes the Needleman-Wunsch global alignment and displays the results in HTML.
     */
    public function renderAsHTML($seq1, $seq2, $full_page=true) {
        $this->hitung($seq1, $seq2);

        if($full_page) { 
            echo '<!DOCTYPE html.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
            echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head>';
            echo '<meta http-equiv="content-type" content="text/html;charset=utf-8" />';
            echo '<meta name="description" content="" />';
            echo '<meta name="keywords" content="" />';
            echo '<title>Needleman-Wunsch Alignment Score Table</title>';
            echo '<style type="text/css">';
            echo '.track { background-color: #c99;font-weight: bold }';
            echo '.seq { background-color: #ccc;}';
            echo '.data { border-collapse: collapse }';
            echo '.data td { border: 1px solid #666; text-align: center; }';
            echo '.align td { text-align: center; }';
            echo '</style>';
            echo '</head>';
            echo '<body>';
        }
        echo '<h3>Table</h3>';
        echo '<table class="data">';
        echo '<tr><td>&nbsp;</td><td>&nbsp;</td>';
        for($i = 0; $i < strlen($seq2); $i++) {
            echo '<td class="seq">'.$seq2[$i].'</td>';
        }
        echo '</tr>';
        for($i = 0; $i < count($this->matrix); $i++) {
            echo "<tr>\n";
            if($i > 0) {
                echo '<td class="seq">'.$seq1[$i-1].'</td>'; 
            } else  {
                echo '<td>&nbsp;</td>';
            }

            foreach($this->matrix[$i] as $r) {
                $str = '<td';
                $str .= $r['track'] ? ' class="track">' : '>';
                $str .= ($r['pointer'] !== null) ? $r['pointer'] : '&nbsp;';
                $str .= '&nbsp;';
                $str .= $r['val'] < 0 ? $r['val'] : '&nbsp;'.$r['val'];
                $str .= '</td>';
                echo $str;
            } 
            
            echo "</tr>\n"; 
        }
        echo "\n</table>";
        echo '<h3> (score = '.$this->optimal_alignment['score'].')</h3>';
        echo '<table class="align">';
        foreach(array('seq2', 'aln', 'seq1') as $k) {
            echo '<tr>';
            foreach($this->optimal_alignment[$k] as $v) {
                echo "<td>$v</td>";
            }
            echo '</tr>';
        }
        echo "\n</table>";

        if($full_page) echo '</body></html>';
    }

    /**
     * penyelarasan
     */
    public function renderAsASCII($seq1, $seq2) {
        $this->hitung($seq1, $seq2);

        echo "Table\n\n";

        $char_array = array();
        for($i = 0; $i < strlen($seq2); $i++) {
            $char_array[] = '   '.$seq2[$i];
        }
        echo "\t\t".implode("\t", $char_array)."\n";
        for($i = 0; $i < count($this->matrix); $i++) {
            if($i > 0) {
                echo $seq1[$i-1]; 
            } else  {
                echo ' ';
            }
            echo "\t";

            $char_array = array();
            foreach($this->matrix[$i] as $r) {
                $str = ($r['pointer'] !== null) ? html_entity_decode($r['pointer'], ENT_QUOTES, 'UTF-8') : ' ';
                $str .= ' ';
                $str .= $r['val'] < 0 ? $r['val'] : ' '.$r['val'];
                $str .= $r['track'] ? '*' : ' ';
                $char_array[] = $str;
            } 
            echo implode("\t", $char_array);
            echo "\n";
        }

        echo "\n(score = ".$this->optimal_alignment['score'].")\n";
        foreach(array('seq2', 'aln', 'seq1') as $k) {
            echo implode(' ', $this->optimal_alignment[$k])."\n";
        }
    }


//     * inisialisasi

    private function init($seq1, $seq2) {
        $this->matrix = array();
        $this->optimal_alignment = array();
		
        for($i = 0; $i < strlen($seq1)+1; $i++) {  //i = seq1
            for($j = 0; $j < strlen($seq2)+1; $j++) { //j = seq2
                $this->matrix[$i][$j] = array(
                    'pointer' => null, 
                    'track' => null, 
                    'val' => 0
                );
            }
        }

        for($i = 0; $i < strlen($seq1); $i++) {
            $this->matrix[$i+1][0]['val'] = ($i+1) * $this->gap_penalty;
        }

        for($j = 0; $j < strlen($seq2); $j++) {
            $this->matrix[0][$j+1]['val'] = ($j+1) * $this->gap_penalty;
        }
    }
}

?>
