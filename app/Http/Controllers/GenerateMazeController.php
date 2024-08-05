<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GenerateMazeController extends Controller
{
    private $n = 10;
    private $m = 10;

    private $maze;

    function show()
    {
        $this->generateMaze("easy");
        for ($i = 0; $i < $this->n; $i++) {
            for ($j = 0; $j < $this->m; $j++) {
                echo '[' . $this->maze[$i][$j][0] . ' ' . $this->maze[$i][$j][1] . ' ' . $this->maze[$i][$j][2] . ' ' . $this->maze[$i][$j][3] . ']';
            }
            echo "<br>";
        }
    }

    function generateMaze($difficulty)
    {
        $this->setDifficulty($difficulty);
        for ($i = 0; $i < $this->n; $i++) {
            for ($j = 0; $j < $this->m; $j++) {
                $this->maze[$i][$j] = array();
                $this->maze[$i][$j][0] = ($i == 0) ? 100 : $this->maze[$i - 1][$j][2];
                $this->maze[$i][$j][1] = ($j != $this->m - 1) ? random_int(1, 10) : 100;
                $this->maze[$i][$j][2] = ($i != $this->n - 1) ? random_int(1, 10) : 100;
                $this->maze[$i][$j][3] = ($j == 0) ? 100 : $this->maze[$i][$j - 1][1];
            }
        }
        $edges = $this->getAllEdges();
        usort($edges, function ($a, $b) {
            return $a['weight'] - $b['weight'];
        });

        $sets = array();
        for ($i = 0; $i < $this->n; $i++) {
            for ($j = 0; $j < $this->m; $j++) {
                $sets[$i][$j] = array($i, $j);
            }
        }

        foreach ($edges as $edge) {
            $u = $edge['u'];
            $v = $edge['v'];

            $setU = $this->findSet($sets, $u[0], $u[1]);
            $setV = $this->findSet($sets, $v[0], $v[1]);

            if ($setU !== $setV || random_int(1, 20) == 1) {
                $this->maze[$u[0]][$u[1]][$edge['directionU']] = "Space";
                $this->maze[$v[0]][$v[1]][$edge['directionV']] = "Space";

                $sets[$setU[0]][$setU[1]] = $setV;
            }
        }
        for ($i = 0; $i < $this->n; $i++) {
            for ($j = 0; $j < $this->m; $j++) {
                for ($k = 0; $k < 4; $k++) {
                    if ($this->maze[$i][$j][$k] != "Space") {
                        $this->maze[$i][$j][$k] = "Wall";
                    }
                }
            }
        }
        $this->maze[0][$this->m - 1][1] = "End";
        $this->maze[$this->n - 1][0][3] = "Start";

        return $this->maze;

    }


    private function getAllEdges()
    {
        $edges = array();

        for ($i = 0; $i < $this->n; $i++) {
            for ($j = 0; $j < $this->m; $j++) {
                if ($j < $this->m - 1) {
                    $edges[] = array(
                        'u' => array($i, $j),
                        'v' => array($i, $j + 1),
                        'directionU' => 1,
                        'directionV' => 3,
                        'weight' => $this->maze[$i][$j][1]
                    );
                }
                if ($i < $this->n - 1) {
                    $edges[] = array(
                        'u' => array($i, $j),
                        'v' => array($i + 1, $j),
                        'directionU' => 2,
                        'directionV' => 0,
                        'weight' => $this->maze[$i][$j][2]
                    );
                }
            }
        }

        return $edges;
    }


    private function findSet(&$sets, $i, $j)
    {
        if ($sets[$i][$j] === array($i, $j)) {
            return array($i, $j);
        } else {
            return $this->findSet($sets, $sets[$i][$j][0], $sets[$i][$j][1]);
        }
    }

    private function setDifficulty($difficulty)
    {
        if ($difficulty == "easy") {
            $this->n = 10;
            $this->m = 10;
        } elseif ($difficulty == "medium") {
            $this->n = 15;
            $this->m = 15;
        } elseif ($difficulty == "hard") {
            $this->n = 20;
            $this->m = 20;
        }
    }

}

