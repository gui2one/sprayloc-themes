<?php
function createLogo()
{
    $str = '<div class="logo-container">
 
  <div class="inner-container">
    <svg>
      <defs>
        <g id="drop">
          <path  d="M 50 0 
             C 15 60, 5 75, 0 100
             A 1 1,  0, 1, 0,  100 100
             C 95 75, 85 60, 50 0
             " 
              stroke="transparent"
              transform = "translate(-50 -100)"/>
        </g>
      </defs>
      <g id="three_drops">
        <use id="drop_1" href="#drop"/>
        <use id="drop_2" x="-11" y="20" href="#drop"/>
        <use id="drop_3" x="11" y="20" href="#drop"/>
      </g>
    </svg>

  </div>
</div>';
    return $str;
}
