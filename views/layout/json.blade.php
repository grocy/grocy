@php
// this is a shoe-horned method to generate a "proper" subview as JSON.

$content = $__env->yieldContent('content');

// Javascript is not JSON, so we need to do some trickery
// to reencode stuff like additional , at the end or
// ' instead of " as field delimter.
$config = "{\n" . $__env->yieldContent('grocyConfigProps') . '}';
$config = preg_replace('/(\n[\t ]*)([a-zA-Z0-9]+):/','${1}"${2}":', $config);
$config = preg_replace('/: *\'(.*?)\',?\n/', ':"${1}",', $config);
$config = preg_replace('/,\n?}$/', '}', $config);
$grocy_options = json_decode($config, true);

$usersettings = $__env->yieldContent('forceUserSettings');
$usersettings = json_decode('{' . $usersettings . '}');
if($usersettings != null)
	$grocy_options["UserSettings"] = $usersettings;

// worst case this burns on the front end.
$viewname = trim($__env->yieldContent('viewJsName'));

echo json_encode([
	'template' => $content,
	'config' => $grocy_options,
	'viewJsName' => $viewname,
	]);
@endphp