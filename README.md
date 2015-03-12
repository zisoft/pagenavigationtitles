Page Navigation With Titles
===========================

Copyright (c) 2012, 2014
Mario Zimmermann <mail@zisoft.de>  
http://www.zisoft.de/software/joomla/pagenavigationtitles.html  


This small plugin is an enhancement of the Joomla! navigation plugin.
It replaces the previous/next navigation links by the titles of the 
corresponding articles.


Plugin Parameters
-----------------

* Turn on/off the left and right arrows (<,>)
* Set custom text for the left and right arrows
* Position of the navigation bar can be set to above, below or both
* Configurable texts in front of the links
* Configurable css class names


HTML Markup And css Styling
---------------------------

The navigation's HTML markup is built in the same way as the native Joomla 
navigation. The names of the css classes can be configured and are predefined 
for Joomla 2.5 so its styling should work right out-of-the box with your 
template. The pretexts in front of the links can be styled separately. The 
following markup and css classes are used:


    <ul class="pagenav">
      <li class="pagenav-prev">
        <a>
          <span class="pagenav-prev-arrow">left-arrow </span>
          <span class="pagenav-prev-pretext">pre-text (prev)</span>
          <span class="pagenav-title">prev link </span>
        </a>
      </li>
      <li class="pagenav-next">
        <a>
          <span class="pagenav-next-pretext">pre-text (next)</span>
          <span class="pagenav-title">next link</span>
          <span class="pagenav-next-arrow">right-arrow</span>
        </a>
      </li>
    </ul>

*NOTE* -- there are no spaces between any of the spans. If you want space use :before or :after and content property or add padding accordingly. 


css Example
-----------

    ul.pagenav, ul.pagenav li {
      list-style: none; list-style-type:none; list-style-image: none;
      margin:0; padding:0; border: 0;
      overflow: hidden;
      width: 100%;
    }
    ul.pagenav {
      border-top:1px solid #606060;
      border-bottom:1px solid #606060;
      clear: both;
    }
    ul.pagenav li.pagenav-prev { float: left; width:auto; }
    ul.pagenav li.pagenav-next { float: right; width:auto; }
