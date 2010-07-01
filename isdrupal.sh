#
#  A simple sh script to determine whether a site is running on Drupal.
#
#  Created by Lasha Dolidze on 03/15/10.
#  Copyright Picktek LLC 2010. All rights reserved.
#  Distributed under GPL license v 2.x or later	 	
#  http://www.gnu.org/licenses/gpl-2.0.html
#

#!/bin/sh

url=$1
INPUT=`curl -fsLI $url 2>&1 | egrep "\<^Location: " | sed 's/Location: \(.*\)/\1/'`
 
if [ "$INPUT" != "" ]
then
 url=`echo $INPUT | tr -d "\r\n"`
 echo "URL: $url"
fi

is_drupal_site() {     
     if curl -fsL $url/misc/drupal.js 2>&1 | grep "drupal.js,v" > /dev/null
     then                                                                                                               
        return 0                                                                                                        
     else                                                                                                               
        return 1                                                                                                        
     fi 
}

is_drupal_5() {
     if curl -fsL $url/modules/system/system.css 2>&1 | grep "system.css,v" > /dev/null
     then
        return 0
     else
        return 1
     fi
}

is_drupal_6() {
     if curl -fsL $url/modules/system/system.js 2>&1 | grep "system.js,v" > /dev/null
     then
        return 0
     else
        return 1
     fi
}

is_drupal_7() {
     if curl -fsL $url/misc/timezone.js 2>&1 | grep "timezone.js,v" > /dev/null
     then
        return 0
     else
        return 1
     fi
}

  

if is_drupal_site                                                                                             
then
  
  #  Check if a website is built with Drupal version 7.
  if is_drupal_7
  then
    echo "Yes, this appears to be a Drupal site version 7."
    exit
  fi
  
  #  Check if a website is built with Drupal version 6.
  if is_drupal_6
  then  
    echo "Yes, this appears to be a Drupal site version 6."
    exit
  fi

  #  Check if a website is built with Drupal version 5.
  if is_drupal_5
  then 
    echo "Yes, this appears to be a Drupal site version 5."	
  else
    echo "Yes, this appears to be a Drupal site version 4."
  fi 
                            
else
  echo "No, this does not appear to be a Drupal site."
fi
