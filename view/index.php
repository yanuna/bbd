<?php
/**

*/

$res = BBB::listMeetings(true);
$msg = strval($res->message);
if (!empty($msg)) {
    echo '<p class="info">BBB: ' . $msg . '</p>';
}
radix::dump($res);
if (!empty($res->meetings)) {
    echo '<h2>Live Meetings</h2>';
    echo '<table>';
    echo '<tr>';
    echo '<th colspan="2">Meeting</th>';
    echo '</tr>';
    foreach ($res->meetings as $m) {
        echo '<tr>';
        echo '<td>';
        radix::dump($m);
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
}


// @todo sort by time, the tail didgits of the name
$ml = BBB::listMeetings();

// Internal MeetingID                                               Time                APVD APVDE RAS Slides Processed            Published           External MeetingID

echo '<h2>' . count($ml) . ' Meetings</h2>';

echo '<table>';
echo '<tr>';
echo '<th colspan="2">Meeting</th>';
echo '<th>Date</th>';
echo '<th>Source</th>';
echo '<th>Archive</th>';
echo '<th>Published</th>';
echo '<th>Internal ID</th>';

echo '</tr>';
foreach ($ml as $mid) {

    $bbm = new BBB_Meeting($mid);
    if (empty($bbm->name)) $bbm->name = '&mdash;';

    echo '<tr>';
    // echo '<td><a href="' . $bbm->playURI() . '" target="_blank"><i class="icon-youtube-play"></i></a></td>';
    echo '<td><a href="' . radix::link('/play?m=' . $mid) . '" target="_blank"><i class="icon-youtube-play"></i></a></td>';
    echo '<td><a href="' . radix::link('/meeting?m=' . $mid) . '">' . $bbm->name . '</a></td>';
    echo '<td>' . $bbm->date . '</td>';

    // Sources
    $stat = $bbm->sourceStat();
    echo '<td>';
    foreach (array('audio','video','slide','share') as $k) {
        if (empty($stat[$k])) continue;
        if (!is_array($stat[$k])) continue;
        if (count($stat[$k])<=0) continue;
        switch ($k) {
        case 'audio':
            echo '<i class="icon-bullhorn" title="Audio Files"></i> ';
            break;
        case 'video':
            echo '<i class="icon-film" title="Video Files"></i> ';
            break;
        case 'slide':
            echo '<i class="icon-picture" title="Slides"></i> ';
            break;
        case 'share':
            echo '<i class="icon-desktop" title="Desktop Sharing"></i> ';
            break;
        }
    }
    echo '</td>';

    // Post Processing Data
    $x = $bbm->archiveStat();
    echo '<td>';
    foreach ($x as $k=>$v) {
        if (!is_array($v)) continue;
        if (count($v)==0) continue;
        switch ($k) {
        case 'audio':
            echo '<i class="icon-bullhorn" title="Audio Files"></i> ';
            break;
        case 'video':
            echo '<i class="icon-film" title="Video Files"></i> ';
            break;
        case 'slide':
            echo '<i class="icon-picture" title="Slides"></i> ';
            break;
        case 'share':
            echo '<i class="icon-desktop" title="Desktop Sharing"></i> ';
            break;
        case 'event':
            echo '<i class="icon-rocket" title="Event Details"></i> ';
        }
    }
    echo '</td>';
    
    // Sanity Files
	// STATUS_DIR=$RAW_PRESENTATION_SRC/recording/status
	// DIRS="recorded archived sanity"
	// for dir in $DIRS; do
	// 	if [ -f $STATUS_DIR/$dir/$meeting.done ]; then
	// 		echo -n "X"
	// 	else 
	// 		echo -n " "
	// 	fi
	// done
	
	// Slide Count

	// Publishing Status
	echo '<td>';
	// $type_list = BBB::listTypes();
	// $stat = $bbm->processStat();
	$type = 'presentation';
	$file = sprintf('%s/processed/%s-%s.done',BBB::STATUS,$mid,$type);
	if (is_file($file)) {
	    echo '<i class="icon-smile" title="The Presentation is Done"></i> ';
	} else {
	    echo '<i class="icon-frown"></i> ';
	}

	// Published
	$type = 'presentation';
	$file = sprintf('%s/published/%s/%s/metadata.xml',BBB::BASE,$type,$mid);
	if (is_file($file)) {
	    echo '<i class="icon-smile" title="The Publishing is Done"></i> ';
	} else {
	    echo '<i class="icon-frown"></i> ';
	}

	echo '</td>';
//	published=""
//	for type in $TYPES; do
//		if [ -f /var/bigbluebutton/published/$type/$recording/metadata.xml ]; then
//			if [ ! -z "$published" ]; then
//				published="$published,"
//			fi
//			published="$published$type"
//		fi
//	done
//    printf "%-17s" $published


    // Internal ID
    echo '<td><a href="' . radix::link('/meeting?m=' . $mid) . '">' . $mid . '</a></td>';

    echo '</tr>';
}
echo '</table>';


// 	if [ -f /var/bigbluebutton/recording/raw/$recording/events.xml ]; then
// 		echo -n "   "
// 		echo -n $(head -n 5 /var/bigbluebutton/recording/raw/$recording/events.xml | grep meetingId | sed s/.*meetingId=\"//g | sed s/\".*//g) | sed -e 's/<[^>]*>//g' -e 's/&lt;/</g' -e 's/&gt;/>/g' -e 's/&amp;/\&/g' -e 's/ \{1,\}/ /g' | tr -d '\n'
// 		if [ $WITHDESC ]; then
// 			echo -n "         "
// 			echo -n $(head -n 5 /var/bigbluebutton/recording/raw/$recording/events.xml | grep description | sed s/.*description=\"//g | sed s/\".*//g) | sed -e 's/<[^>]*>//g' -e 's/&lt;/</g' -e 's/&gt;/>/g' -e 's/&amp;/\&/g' -e 's/ \{1,\}/ /g' | tr -d '\n'
// 		fi
// 	fi

// Get Process List of tomcat User, It will be Background Commands
// ps fU tomcat6 -o "%c%a" | grep -v COMMAND | grep -v logging.properties


// if tail -n 20 /var/log/bigbluebutton/bbb-web.log | grep -q "is recorded. Process it."; then
//     echo -n "Last meeting processed (bbb-web.log): "
//     tail -n 20 /var/log/bigbluebutton/bbb-web.log | grep "is recorded. Process it." | sed "s/.*\[//g" | sed "s/\].*//g"
// fi