<?php

$finder = PhpCsFixer\Finder::create()
	->exclude(['packages'])
	->ignoreVCSIgnored(true)
	->files()->name('*.php')
	->in(__DIR__);

$cfg = new PhpCsFixer\Config();
return $cfg
	->setRules([
		'@PSR2' => true,
		'array_indentation' => true,
		'array_syntax' => ['syntax' => 'short'],
		'combine_consecutive_unsets' => true,
		'class_attributes_separation' => true,
		'class_attributes_separation' => ['elements' => ['const' => 'none', 'property' => 'none']],
		'multiline_whitespace_before_semicolons' => false,
		'single_quote' => true,
		'blank_line_after_opening_tag' => true,
		'curly_braces_position' => [
			'control_structures_opening_brace' => 'next_line_unless_newline_at_signature_end',
			'anonymous_functions_opening_brace' => 'next_line_unless_newline_at_signature_end'
		],
		'control_structure_continuation_position' => [
			'position' => 'next_line'
		],
		'cast_spaces' => [
			'space' => 'none'
		],
		'concat_space' => ['spacing' => 'one'],
		'declare_equal_normalize' => true,
		'type_declaration_spaces' => true,
		'single_line_comment_style' => ['comment_types' => ['hash']],
		'include' => true,
		'lowercase_cast' => true,
		'no_leading_import_slash' => true,
		'no_leading_namespace_whitespace' => true,
		'no_multiline_whitespace_around_double_arrow' => true,
		'no_spaces_around_offset' => true,
		'no_whitespace_before_comma_in_array' => true,
		'no_whitespace_in_blank_line' => true,
		'object_operator_without_whitespace' => true,
		'blank_lines_before_namespace' => true,
		'ternary_operator_spaces' => true,
		'trim_array_spaces' => true,
		'unary_operator_spaces' => true,
		'whitespace_after_comma_in_array' => true,
		'no_trailing_comma_in_singleline' => true
	])
	->setIndent("\t")
	->setLineEnding("\n")
	->setUsingCache(false)
	->setFinder($finder);
