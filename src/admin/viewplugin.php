<?php

define('ROOT', '../');
session_start();

require_once ROOT . 'config.php';
require_once ROOT . 'includes/database.php';
require_once ROOT . 'includes/func.php';

ensure_loggedin();

/// Is this an ajax call?
$ajax = isset($_GET['ajax']) || isset($_SERVER['HTTP_X_PJAX']) || isset($_SERVER['X-PJAX']);

// If not........
if (!$ajax)
{
    send_header();
}

/// Check for the plugin in $_GET
if (!isset($_GET['plugin']))
{
    err ('No plugin provided.');
}

else
{
    // Load the provided plugin
    $plugin = loadPlugin($_GET['plugin']);

    /// Can we access it?
    if (!can_admin_plugin($plugin))
    {
        err ('Invalid plugin.');
    }

    else
    {
?>

<?php
    if (!$ajax)
    {
        echo '            <div class="row-fluid">
';
        send_admin_sidebar();
        echo '
                <div class="span8" id="plugin-content">';
    }
?>

                    <div class="span5">

                        <form action="/admin/plugin/<?php echo $plugin->getName(); ?>/update" method="post" class="form-horizontal">
                            <legend>
                                Basic information
                            </legend>

                            <div class="control-group">
                                <label class="control-label" for="name">Plugin name</label>

                                <div class="controls">
                                    <input type="text" name="name" value="<?php echo $plugin->getName(); ?>" id="name" disabled />
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="authors">Authors</label>

                                <div class="controls">
                                    <input type="text" name="authors" value="<?php echo $plugin->getAuthors(); ?>" id="authors" />
                                </div>
                            </div>
<?php

$graphs = $plugin->getAllGraphs();
$index = 0;
foreach ($graphs as $graph)
{
    $index ++ ; // start at 1 as well

    // convenient data so we aren't constantly using accessors
    $id = $graph->getID();
    $name = htmlentities($graph->getName());
    $displayName = $name; // TODO
    $type = $graph->getType();
    $isActive = $graph->isActive();
echo '
                            <legend>
                                Custom graph #' . $index . '
                            </legend>

                            <div class="control-group">
                                <label class="control-label" for="' . $id . '-name">Internal Name</label>

                                <div class="controls">
                                    <input type="text" id="' . $id . '-name" value="' . $name . '" disabled />
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="' . $id . '-displayname">Display Name</label>

                                <div class="controls">
                                    <input type="text" name="' . $id . '-displayname" id="' . $id . '-displayname" value="' . $displayName . '" />
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="' . $id . '-type">Type</label>

                                <div class="controls">
                                    <select name="' . $id . '-type" id="' . $id . '-type">
                                        <option value="' . GraphType::Line . '"' . ($type == GraphType::Line ? ' selected' : '') . '>Line</option>
                                        <option value="' . GraphType::Area . '"' . ($type == GraphType::Area ? ' selected' : '') . '>Area</option>
                                        <option value="' . GraphType::Column . '"' . ($type == GraphType::Column ? ' selected' : '') . '>Column</option>
                                        <option value="' . GraphType::Pie . '"' . ($type == GraphType::Pie ? ' selected' : '') . '>Pie</option>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="' . $id . '-active">Active</label>

                                <div class="controls">
                                    <label class="checkbox">
                                        <input type="checkbox" name="' . $id . '-active" id="' . $id . '-active" value="1"' . ($isActive ? ' selected' : '') . '>
                                    </label>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="' . $id . '-scale">Scale</label>

                                <div class="controls">
                                    <label class="checkbox inline">
                                        <input type="checkbox" name="' . $id . '-scale" id="' . $id . '-scale" value="linear"> Linear
                                    </label>
                                    <label class="checkbox inline">
                                        <input type="checkbox" name="' . $id . '-scale" value="log"> Logarithmic
                                    </label>
                                </div>
                            </div>

';
}
?>

                            <div class="form-actions">
                                <input type="submit" name="submit" value="Save changes" class="btn btn-primary" />
                                <a href="/admin/" class="btn">Cancel</a>
                            </div>

                        </form>

                    </div>

                <?php
                if (!$ajax)
                {
                    echo '
                </div>

            </div>';
                }
                ?>


<?php
    }

}

if (!$ajax)
{
    send_footer();
}