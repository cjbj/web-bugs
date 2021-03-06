<h2>Patch <?php echo clean($patch_name); ?> for <?php echo clean($package_name); ?> Bug #<?php echo $bug_id; ?></h2>
<p><b>Patch version <?php echo format_date($revision); ?></b></p>
<a href="bug.php?id=<?php echo $bug_id; ?>">Return to Bug #<?php echo $bug_id; ?></a>
| <a href="patch-display.php?bug_id=<?php echo $bug_id; ?>&amp;patch=<?php echo $patch_name_url; ?>&amp;revision=<?php echo urlencode($revision); ?>&amp;download=1">Download this patch</a><br />
<?php
if (count($obsoletedby)) {
    echo '<div class="warnings">This patch is obsolete</div><p>Obsoleted by patches:<ul>';
    foreach ($obsoletedby as $betterpatch) {
        echo '<li><a href="patch-display.php?patch=',
             urlencode($betterpatch['patch']),
             '&amp;bug_id=', $bug_id, '&amp;revision=', $betterpatch['revision'],
             '">', htmlspecialchars($betterpatch['patch']), ', revision ',
             format_date($betterpatch['revision']), '</a></li>';
    }
    echo '</ul></p>';
}
if (count($obsoletes)) {
    echo '<div class="warnings">This patch renders other patches obsolete</div>',
         '<p>Obsolete patches:<ul>';
    foreach ($obsoletes as $betterpatch) {
        echo '<li><a href="patch-display.php?patch=',
             urlencode($betterpatch['obsolete_patch']),
             '&amp;bug_id=', $bug_id,
             '&amp;revision=', $betterpatch['obsolete_revision'],
             '">', htmlspecialchars($betterpatch['obsolete_patch']), ', revision ',
             format_date($betterpatch['obsolete_revision']), '</a></li>';
    }
    echo '</ul></p>';
}
?>
Patch Revisions:
<?php foreach ($revisions as $i => $revision) { ?>
<a href="patch-display.php?bug_id=<?php echo $bug_id; ?>&amp;patch=<?php
    echo $patch_name_url; ?>&amp;revision=<?php echo urlencode($revision[0]); ?>"><?php
    echo format_date($revision[0]); ?></a><?php if ($i < count($revisions) - 1) echo ' | '; ?>
<?php } ?>
<h3>Developer: <?php echo $handle; ?></h3>
<pre>
<?php if ($d->isEmpty()) echo 'Diffs are identical!'; else {
    echo $diff->render($d);
}
?>
</pre>
