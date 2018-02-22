<?php 
class fmt {
	function __construct($locale = "") {
		setlocale(LC_NUMERIC, $locale);
	}

	function amount($amount, $dec = 1) {
		$u = "";
		if ($amount) {
			$e = log10($amount);
			$f = intval($e - ($e % 3));
			if ($f) {
				$amount /= pow(10, $f);
				$u = ["","K","M","G","T","P"][$f/3];
			}
		}
		if (!$u) {
			$dec = 0;
		}
		return number_format($amount, $dec) ." ". $u;
	}
	
	function elapsed($seconds) {
		return sprintf("%7.3f s", round($seconds, 3));
	}

	function bytes($bytes) {
		return $this->amount($bytes, 3) . "B";
	}

	function rate($count, $elapsed) {
		return $this->amount($count/$elapsed) . "TPS";
	}
}

class event {
	public $name;
	public $time;
	public $mem;
	public $count;

	function __construct($name) {
		$this->name = $name;
		$this->time = microtime(true);
		$this->mem = [
			"cur" => memory_get_usage(), 
			"peak" => memory_get_peak_usage()
		];
	}

	function __toString() {
		return (string) $this->name;
	}

	function diff(event $event) {
		$event = clone $event;
		$event->name = "Difference between '$this' and '$event'";
		$event->time = $this->time - $event->time;
		$event->mem = [
			"cur" => $this->mem["cur"] - $event->mem["cur"],
			"peak" => $this->mem["peak"] - $event->mem["peak"]
		];
		$event->count = $this->count - $event->count;
		return $event;
	}
}

class timer {
	private $tpt;
	private $fmt;
	private $events;

	function __construct(float $tpt, fmt $fmt) {
		$this->tpt = $tpt;
		$this->fmt = $fmt;
		$this->event("init");
	}

	private function event($event) {
		return $this->events[] = new event($event);
	}

	private function start() {
		return $this->event("start");
	}

	private function stop($event) {
		return $this->event($event);
	}
	
	function unit(unit $unit) {
		$this->start();
		$unit($this->tpt);
		$record = $this->stop($unit);
		$record->count = $unit->count();
		return $this;
	}

	function __toString() {
		$current = end($this->events);
		
		if ($current == "init") {
			return sprintf(
				"	@ Approx s/Test: %3.1f s\n".
				"	@ Cur Memory:    %s\n".
				"	@ Peak Memory:   %s\n\n",
				$this->tpt,
				$this->fmt->bytes($current->mem["cur"]),
				$this->fmt->bytes($current->mem["peak"])
			);
		}

		$start = reset($this->events);
		if (count($this->events) < 3) {
			$previous = $start;
		} else {
			$previous = $this->events[count($this->events)-2];
		}

		$diff_start = $current->diff($start);
		$diff_prev = $current->diff($previous);

		return sprintf("\n".
				"	@ Count:       %s\n".
				"	@ Rate:        %s\n".
				"	@ Elapsed:     +%s = %s\n".
				"	@ Cur Memory:  +%9s = %9s\n".
				"	@ Peak Memory: +%9s = %9s\n\n",
				$this->fmt->amount($current->count * $this->tpt, 0),
				$this->fmt->rate($current->count * $this->tpt, $diff_prev->time),
				$this->fmt->elapsed($diff_prev->time),
				$this->fmt->elapsed($diff_start->time),
				$this->fmt->bytes($diff_prev->mem["cur"]),
				$this->fmt->bytes($current->mem["cur"]),
				$this->fmt->bytes($diff_prev->mem["peak"]),
				$this->fmt->bytes($current->mem["peak"])
		);
	}
}

class unit implements Countable {
	private $test;
	private $exec;
	private $data;
	private $count;

	function __construct(string $test, int $count, string $data, callable $exec) {
		$this->test = $test;
		$this->count = $count;
		$this->data = $data;
		$this->exec = $exec;
	}

	function __invoke(float $time) {
		$cnt = $this->count * $time;
		$run = round($cnt/10);
		for ($i = 0; $i < 10; ++$i) {
			for ($j = 0; $j < $run; ++$j) {
				($this->exec)($this->data);
			}
			print ".";
		}
	}
	
	function count() {
		return $this->count;
	}

	function __toString() {
		return (string) $this->test;
	}
}

# --->8--- #

$timer = new timer($argv[1] ?? 1, new fmt);
$units = glob(__DIR__."/unit/*.php");

printf("Running %d units...\n%s\n", count($units), $timer);

foreach ($units as $unit) {
	$unit = include $unit;
	print $unit;
	print $timer->unit($unit);
}

printf("Done\n");
