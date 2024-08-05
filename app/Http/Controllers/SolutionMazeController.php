<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use SplPriorityQueue;

class SolutionMazeController extends Controller
{
    private $n;
    private $m;
    private $maze;

    private $start = array();
    private $end = array();

    private $queue = array();
    private $path = array();

    public function solution($solution, Request $request)
    {
        $data = $request->json()->all();
        $this->maze = $data['data'];
        $this->n = sizeof($this->maze);
        $this->m = sizeof($this->maze);
        if ($solution == 'bfs') {
            $this->bfs();
        } else if ($solution == "dijkstra") {
            $this->dijkstra();
        }
        return $this->path;
    }

    private function bfs()
    {
        $edges = $this->getEdges();
        $this->queue = [[$this->start[0], $this->start[1], []]];
        while (!empty($this->queue)) {
            [$NodeX, $NodeY, $currentPath] = array_shift($this->queue);
            if ($NodeX == $this->end[0] && $NodeY == $this->end[1]) {
                $currentPath = array_merge($currentPath, [$NodeY . ',' . $NodeX]);
                foreach ($currentPath as $pair) {
                    list($x, $y) = explode(",", $pair);
                    $this->path[] = ['x' => (int)$x, 'y' => (int)$y];
                }
                $this->addMirrors();
                return;
            }
            $this->queueNodes($NodeX, $NodeY, $edges, $currentPath);
        }
    }

    private function getEdges()
    {
        $edges = array();
        for ($i = 0; $i < $this->n; $i++) {
            for ($j = 0; $j < $this->m; $j++) {
                if ($this->maze[$i][$j][3] == "Start") {
                    $this->start[0] = $i;
                    $this->start[1] = $j;
                }
                if ($this->maze[$i][$j][1] == "End") {
                    $this->end[0] = $i;
                    $this->end[1] = $j;
                }
                if ($j < $this->m - 1 && $this->maze[$i][$j][1] == "Space") {
                    $edges[] = array(
                        'u' => array($i, $j),
                        'v' => array($i, $j + 1),
                        'directionU' => 1,
                        'directionV' => 3,
                    );
                }
                if ($i < $this->n - 1 && $this->maze[$i][$j][2] == "Space") {
                    $edges[] = array(
                        'u' => array($i, $j),
                        'v' => array($i + 1, $j),
                        'directionU' => 2,
                        'directionV' => 0,
                    );
                }
            }
        }
        return $edges;
    }

    private function queueNodes($i, $j, &$edges, $currentPath)
    {
        foreach ($edges as $key => $edge) {
            if ($edge['u'][0] == $i && $edge['u'][1] == $j) {
                $nextNode = $edge['v'];
                unset($edges[$key]);
            } elseif ($edge['v'][0] == $i && $edge['v'][1] == $j) {
                $nextNode = $edge['u'];
                unset($edges[$key]);
            } else {
                continue;
            }

            $nodeString = $nextNode[1] . ',' . $nextNode[0];
            if (!in_array($nodeString, $currentPath, true)) {
                $newPath = array_merge($currentPath, [$j . ',' . $i]);
                array_push($this->queue, [$nextNode[0], $nextNode[1], $newPath]);
            }
        }
        $edges = array_values($edges);
    }

    private function addMirrors()
    {
        $direction = "L->R";
        for ($i = 0; $i < sizeof($this->path) - 1; $i++) {
            $currentY = $this->path[$i]['y'];
            $nextY = $this->path[$i + 1]['y'];

            $currentX = $this->path[$i]['x'];
            $nextX = $this->path[$i + 1]['x'];

            if ($currentY != $nextY) {
                if ($direction == "L->R") {
                    $this->path[$i]['m'] = ($currentY > $nextY) ? 2 : 1;
                    $direction = ($currentY > $nextY) ? "D->U" : "U->D";
                    if ($currentY < $nextY) {
                        $this->path[$i]['rotate'] = true;
                    }
                } elseif ($direction == "R->L") {
                    $this->path[$i]['m'] = ($currentY > $nextY) ? 1 : 2;
                    $direction = ($currentY > $nextY) ? "D->U" : "U->D";
                    if ($currentY < $nextY) {
                        $this->path[$i]['rotate'] = true;
                    }
                }
            } elseif ($currentX != $nextX) {
                if ($direction == "D->U") {
                    $this->path[$i]['m'] = ($currentX > $nextX) ? 1 : 2;
                    $direction = ($currentX > $nextX) ? "R->L" : "L->R";
                    $this->path[$i]['rotate'] = true;
                } elseif ($direction == "U->D") {
                    $this->path[$i]['m'] = ($currentX > $nextX) ? 2 : 1;
                    $direction = ($currentX > $nextX) ? "R->L" : "L->R";
                }
            }
            if ($i + 1 == sizeof($this->path) - 1 && $direction == "D->U") {
                $this->path[$i + 1]['m'] = 2;
                $this->path[$i + 1]['rotate'] = true;
            }
        }
    }

    private function dijkstra()
    {
        $edges = $this->getEdges();
        $distances = array_fill(0, $this->n * $this->m, PHP_INT_MAX);
        $distances[$this->start[0] * $this->m + $this->start[1]] = 0;

        $pq = new SplPriorityQueue();
        $pq->insert([$this->start[0], $this->start[1]], 0);

        while (!$pq->isEmpty()) {
            [$NodeX, $NodeY] = $pq->extract();

            foreach ($edges as $edge) {
                if ($edge['u'][0] == $NodeX && $edge['u'][1] == $NodeY) {
                    $nextNode = $edge['v'];
                } elseif ($edge['v'][0] == $NodeX && $edge['v'][1] == $NodeY) {
                    $nextNode = $edge['u'];
                } else {
                    continue;
                }

                $nextX = $nextNode[0];
                $nextY = $nextNode[1];
                $weight = 1;

                if ($distances[$NodeX * $this->m + $NodeY] + $weight < $distances[$nextX * $this->m + $nextY]) {
                    $distances[$nextX * $this->m + $nextY] = $distances[$NodeX * $this->m + $NodeY] + $weight;
                    $pq->insert([$nextX, $nextY], -$distances[$nextX * $this->m + $nextY]);
                }
            }
        }

        $currentNode = $this->end;
        while ($currentNode != $this->start) {
            $this->path[] = ['x' => $currentNode[1], 'y' => $currentNode[0]];
            $currentNode = $this->getParentNode($currentNode, $edges, $distances);
        }
        $this->path[] = ['x' => $this->start[1], 'y' => $this->start[0]];
        $this->path = array_reverse($this->path);

        $this->addMirrors();
    }

    private function getParentNode($node, $edges, $distances)
    {
        foreach ($edges as $edge) {
            if ($edge['v'] == $node && $distances[$edge['u'][0] * $this->m + $edge['u'][1]] + 1 == $distances[$node[0] * $this->m + $node[1]]) {
                return $edge['u'];
            } elseif ($edge['u'] == $node && $distances[$edge['v'][0] * $this->m + $edge['v'][1]] + 1 == $distances[$node[0] * $this->m + $node[1]]) {
                return $edge['v'];
            }
        }

        return null;
    }
}
