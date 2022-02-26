<?php

namespace Bermuda\ErrorHandler;

trait ErrorListener
{
    private int $priority = 1;
  
    public function __construct(int $priority = 1) {
      $this->priority = $priority;
    }
  
    public function getPriority(): int
    {
      return $this->priority;
    }
}
