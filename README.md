Page Navigation With Titles
===========================

Copyright (c) 2012  
Mario Zimmermann <mail@zisoft.de>  
http://www.zisoft.de/software/joomla/pagenavigationtitles.html  


This small plugin is an enhancement of the Joomla! navigation plugin.
It replaces the previous/next navigation links by the titles of the 
corresponding articles.


Plugin Parameters
-----------------

* Turn on/off the left and right arrows (<,>)
* Position of the navigation bar can be set to above, below or both
* Configurable texts in front of the links


HTML Markup And css Styling
---------------------------

The navigation's HTML markup is built in the same way as the native 
Joomla navigation and uses the same css classes so its styling should 
work right out-of-the box with your template. The pretexts in front 
of the links can be styled separately. The following markup and css 
classes are used:


    <ul class="pagenav">
      <li class="pagenav-prev">
        left-arrow 
        <span class="pagenav-prev-pretext">pre-text (prev)</span>
        <a>prev link</a>
      </li>
      <li class="pagenav-next">
        <span class="pagenav-next-pretext">pre-text (next)</span>
        <a>next link</a>
        right-arrow
      </li>
    </ul>


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
    }
    ul.pagenav li.pagenav-prev { float: left; width:auto; }
    ul.pagenav li.pagenav-next { float: right; width:auto; }
