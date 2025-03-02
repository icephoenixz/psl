<?php

declare(strict_types=1);

namespace Psl\Internal;

use Closure;

use function array_merge;
use function class_exists;
use function defined;
use function dirname;
use function function_exists;
use function interface_exists;
use function spl_autoload_register;
use function spl_autoload_unregister;
use function trait_exists;

/**
 * This class SHOULD NOT use any Psl functions, or classes.
 *
 * @codeCoverageIgnore
 *
 * @internal
 */
final class Loader
{
    public const CONSTANTS = [
        'Psl\\Internal\\ALPHABET_BASE64' => 'Psl/Internal/constants.php',
        'Psl\\Internal\\ALPHABET_BASE64_URL' => 'Psl/Internal/constants.php',
        'Psl\\Internal\\CASE_FOLD' => 'Psl/Internal/constants.php',
        'Psl\\Math\\INT64_MAX' => 'Psl/Math/constants.php',
        'Psl\\Math\\INT64_MIN' => 'Psl/Math/constants.php',
        'Psl\\Math\\INT53_MAX' => 'Psl/Math/constants.php',
        'Psl\\Math\\INT53_MIN' => 'Psl/Math/constants.php',
        'Psl\\Math\\INT32_MAX' => 'Psl/Math/constants.php',
        'Psl\\Math\\INT32_MIN' => 'Psl/Math/constants.php',
        'Psl\\Math\\INT16_MAX' => 'Psl/Math/constants.php',
        'Psl\\Math\\INT16_MIN' => 'Psl/Math/constants.php',
        'Psl\\Math\\UINT32_MAX' => 'Psl/Math/constants.php',
        'Psl\\Math\\UINT16_MAX' => 'Psl/Math/constants.php',
        'Psl\\Math\\PI' => 'Psl/Math/constants.php',
        'Psl\\Math\\E' => 'Psl/Math/constants.php',
        'Psl\\Math\\INFINITY' => 'Psl/Math/constants.php',
        'Psl\\Math\\NAN' => 'Psl/Math/constants.php',
        'Psl\\Str\\ALPHABET' => 'Psl/Str/constants.php',
        'Psl\\Str\\ALPHABET_ALPHANUMERIC' => 'Psl/Str/constants.php',
        'Psl\\Filesystem\\SEPARATOR' => 'Psl/Filesystem/constants.php',
    ];

    public const FUNCTIONS = [
        'Psl\\Dict\\associate' => 'Psl/Dict/associate.php',
        'Psl\\Dict\\count_values' => 'Psl/Dict/count_values.php',
        'Psl\\Dict\\drop' => 'Psl/Dict/drop.php',
        'Psl\\Dict\\drop_while' => 'Psl/Dict/drop_while.php',
        'Psl\\Dict\\equal' => 'Psl/Dict/equal.php',
        'Psl\\Dict\\filter' => 'Psl/Dict/filter.php',
        'Psl\\Dict\\filter_nulls' => 'Psl/Dict/filter_nulls.php',
        'Psl\\Dict\\filter_keys' => 'Psl/Dict/filter_keys.php',
        'Psl\\Dict\\filter_with_key' => 'Psl/Dict/filter_with_key.php',
        'Psl\\Dict\\flatten' => 'Psl/Dict/flatten.php',
        'Psl\\Dict\\flip' => 'Psl/Dict/flip.php',
        'Psl\\Dict\\from_entries' => 'Psl/Dict/from_entries.php',
        'Psl\\Dict\\from_iterable' => 'Psl/Dict/from_iterable.php',
        'Psl\\Dict\\from_keys' => 'Psl/Dict/from_keys.php',
        'Psl\\Dict\\group_by' => 'Psl/Dict/group_by.php',
        'Psl\\Dict\\map' => 'Psl/Dict/map.php',
        'Psl\\Dict\\map_keys' => 'Psl/Dict/map_keys.php',
        'Psl\\Dict\\map_with_key' => 'Psl/Dict/map_with_key.php',
        'Psl\\Dict\\merge' => 'Psl/Dict/merge.php',
        'Psl\\Dict\\partition' => 'Psl/Dict/partition.php',
        'Psl\\Dict\\partition_with_key' => 'Psl/Dict/partition_with_key.php',
        'Psl\\Dict\\pull' => 'Psl/Dict/pull.php',
        'Psl\\Dict\\pull_with_key' => 'Psl/Dict/pull_with_key.php',
        'Psl\\Dict\\reindex' => 'Psl/Dict/reindex.php',
        'Psl\\Dict\\select_keys' => 'Psl/Dict/select_keys.php',
        'Psl\\Dict\\slice' => 'Psl/Dict/slice.php',
        'Psl\\Dict\\sort' => 'Psl/Dict/sort.php',
        'Psl\\Dict\\sort_by' => 'Psl/Dict/sort_by.php',
        'Psl\\Dict\\sort_by_key' => 'Psl/Dict/sort_by_key.php',
        'Psl\\Dict\\take' => 'Psl/Dict/take.php',
        'Psl\\Dict\\take_while' => 'Psl/Dict/take_while.php',
        'Psl\\Dict\\unique' => 'Psl/Dict/unique.php',
        'Psl\\Dict\\unique_by' => 'Psl/Dict/unique_by.php',
        'Psl\\Dict\\unique_scalar' => 'Psl/Dict/unique_scalar.php',
        'Psl\\Dict\\diff' => 'Psl/Dict/diff.php',
        'Psl\\Dict\\diff_by_key' => 'Psl/Dict/diff_by_key.php',
        'Psl\\Dict\\intersect' => 'Psl/Dict/intersect.php',
        'Psl\\Dict\\intersect_by_key' => 'Psl/Dict/intersect_by_key.php',
        'Psl\\Fun\\after' => 'Psl/Fun/after.php',
        'Psl\\Fun\\identity' => 'Psl/Fun/identity.php',
        'Psl\\Fun\\lazy' => 'Psl/Fun/lazy.php',
        'Psl\\Fun\\pipe' => 'Psl/Fun/pipe.php',
        'Psl\\Fun\\rethrow' => 'Psl/Fun/rethrow.php',
        'Psl\\Fun\\tap' => 'Psl/Fun/tap.php',
        'Psl\\Fun\\when' => 'Psl/Fun/when.php',
        'Psl\\Internal\\suppress' => 'Psl/Internal/suppress.php',
        'Psl\\Internal\\box' => 'Psl/Internal/box.php',
        'Psl\\Str\\Internal\\validate_offset' => 'Psl/Str/Internal/validate_offset.php',
        'Psl\\Iter\\all' => 'Psl/Iter/all.php',
        'Psl\\Iter\\any' => 'Psl/Iter/any.php',
        'Psl\\Iter\\apply' => 'Psl/Iter/apply.php',
        'Psl\\Iter\\contains' => 'Psl/Iter/contains.php',
        'Psl\\Iter\\contains_key' => 'Psl/Iter/contains_key.php',
        'Psl\\Iter\\count' => 'Psl/Iter/count.php',
        'Psl\\Iter\\first' => 'Psl/Iter/first.php',
        'Psl\\Iter\\first_key' => 'Psl/Iter/first_key.php',
        'Psl\\Iter\\is_empty' => 'Psl/Iter/is_empty.php',
        'Psl\\Iter\\last' => 'Psl/Iter/last.php',
        'Psl\\Iter\\last_key' => 'Psl/Iter/last_key.php',
        'Psl\\Iter\\random' => 'Psl/Iter/random.php',
        'Psl\\Iter\\reduce' => 'Psl/Iter/reduce.php',
        'Psl\\Iter\\reduce_keys' => 'Psl/Iter/reduce_keys.php',
        'Psl\\Iter\\reduce_with_keys' => 'Psl/Iter/reduce_with_keys.php',
        'Psl\\Iter\\rewindable' => 'Psl/Iter/rewindable.php',
        'Psl\\Iter\\search' => 'Psl/Iter/search.php',
        'Psl\\Iter\\to_iterator' => 'Psl/Iter/to_iterator.php',
        'Psl\\Vec\\chunk' => 'Psl/Vec/chunk.php',
        'Psl\\Vec\\chunk_with_keys' => 'Psl/Vec/chunk_with_keys.php',
        'Psl\\Vec\\concat' => 'Psl/Vec/concat.php',
        'Psl\\Vec\\drop' => 'Psl/Vec/drop.php',
        'Psl\\Vec\\enumerate' => 'Psl/Vec/enumerate.php',
        'Psl\\Vec\\fill' => 'Psl/Vec/fill.php',
        'Psl\\Vec\\filter' => 'Psl/Vec/filter.php',
        'Psl\\Vec\\filter_keys' => 'Psl/Vec/filter_keys.php',
        'Psl\\Vec\\filter_nulls' => 'Psl/Vec/filter_nulls.php',
        'Psl\\Vec\\filter_with_key' => 'Psl/Vec/filter_with_key.php',
        'Psl\\Vec\\flat_map' => 'Psl/Vec/flat_map.php',
        'Psl\\Vec\\keys' => 'Psl/Vec/keys.php',
        'Psl\\Vec\\partition' => 'Psl/Vec/partition.php',
        'Psl\\Vec\\range' => 'Psl/Vec/range.php',
        'Psl\\Vec\\reductions' => 'Psl/Vec/reductions.php',
        'Psl\\Vec\\map' => 'Psl/Vec/map.php',
        'Psl\\Vec\\map_with_key' => 'Psl/Vec/map_with_key.php',
        'Psl\\Vec\\reproduce' => 'Psl/Vec/reproduce.php',
        'Psl\\Vec\\reverse' => 'Psl/Vec/reverse.php',
        'Psl\\Vec\\shuffle' => 'Psl/Vec/shuffle.php',
        'Psl\\Vec\\slice' => 'Psl/Vec/slice.php',
        'Psl\\Vec\\sort' => 'Psl/Vec/sort.php',
        'Psl\\Vec\\sort_by' => 'Psl/Vec/sort_by.php',
        'Psl\\Vec\\take' => 'Psl/Vec/take.php',
        'Psl\\Vec\\values' => 'Psl/Vec/values.php',
        'Psl\\Vec\\zip' => 'Psl/Vec/zip.php',
        'Psl\\Math\\abs' => 'Psl/Math/abs.php',
        'Psl\\Math\\base_convert' => 'Psl/Math/base_convert.php',
        'Psl\\Math\\ceil' => 'Psl/Math/ceil.php',
        'Psl\\Math\\clamp' => 'Psl/Math/clamp.php',
        'Psl\\Math\\cos' => 'Psl/Math/cos.php',
        'Psl\\Math\\acos' => 'Psl/Math/acos.php',
        'Psl\\Math\\div' => 'Psl/Math/div.php',
        'Psl\\Math\\exp' => 'Psl/Math/exp.php',
        'Psl\\Math\\floor' => 'Psl/Math/floor.php',
        'Psl\\Math\\from_base' => 'Psl/Math/from_base.php',
        'Psl\\Math\\log' => 'Psl/Math/log.php',
        'Psl\\Math\\max' => 'Psl/Math/max.php',
        'Psl\\Math\\max_by' => 'Psl/Math/max_by.php',
        'Psl\\Math\\maxva' => 'Psl/Math/maxva.php',
        'Psl\\Math\\mean' => 'Psl/Math/mean.php',
        'Psl\\Math\\median' => 'Psl/Math/median.php',
        'Psl\\Math\\min' => 'Psl/Math/min.php',
        'Psl\\Math\\min_by' => 'Psl/Math/min_by.php',
        'Psl\\Math\\minva' => 'Psl/Math/minva.php',
        'Psl\\Math\\round' => 'Psl/Math/round.php',
        'Psl\\Math\\sin' => 'Psl/Math/sin.php',
        'Psl\\Math\\asin' => 'Psl/Math/asin.php',
        'Psl\\Math\\sqrt' => 'Psl/Math/sqrt.php',
        'Psl\\Math\\sum' => 'Psl/Math/sum.php',
        'Psl\\Math\\sum_floats' => 'Psl/Math/sum_floats.php',
        'Psl\\Math\\tan' => 'Psl/Math/tan.php',
        'Psl\\Math\\atan' => 'Psl/Math/atan.php',
        'Psl\\Math\\atan2' => 'Psl/Math/atan2.php',
        'Psl\\Math\\to_base' => 'Psl/Math/to_base.php',
        'Psl\\Result\\collect_stats' => 'Psl/Result/collect_stats.php',
        'Psl\\Result\\wrap' => 'Psl/Result/wrap.php',
        'Psl\\Regex\\capture_groups' => 'Psl/Regex/capture_groups.php',
        'Psl\\Regex\\every_match' => 'Psl/Regex/every_match.php',
        'Psl\\Regex\\first_match' => 'Psl/Regex/first_match.php',
        'Psl\\Regex\\split' => 'Psl/Regex/split.php',
        'Psl\\Regex\\matches' => 'Psl/Regex/matches.php',
        'Psl\\Regex\\replace' => 'Psl/Regex/replace.php',
        'Psl\\Regex\\replace_with' => 'Psl/Regex/replace_with.php',
        'Psl\\Regex\\replace_every' => 'Psl/Regex/replace_every.php',
        'Psl\\Regex\\Internal\\get_preg_error' => 'Psl/Regex/Internal/get_preg_error.php',
        'Psl\\Regex\\Internal\\call_preg' => 'Psl/Regex/Internal/call_preg.php',
        'Psl\\SecureRandom\\bytes' => 'Psl/SecureRandom/bytes.php',
        'Psl\\SecureRandom\\float' => 'Psl/SecureRandom/float.php',
        'Psl\\SecureRandom\\int' => 'Psl/SecureRandom/int.php',
        'Psl\\SecureRandom\\string' => 'Psl/SecureRandom/string.php',
        'Psl\\PseudoRandom\\float' => 'Psl/PseudoRandom/float.php',
        'Psl\\PseudoRandom\\int' => 'Psl/PseudoRandom/int.php',
        'Psl\\Str\\Byte\\capitalize' => 'Psl/Str/Byte/capitalize.php',
        'Psl\\Str\\Byte\\capitalize_words' => 'Psl/Str/Byte/capitalize_words.php',
        'Psl\\Str\\Byte\\chr' => 'Psl/Str/Byte/chr.php',
        'Psl\\Str\\Byte\\chunk' => 'Psl/Str/Byte/chunk.php',
        'Psl\\Str\\Byte\\compare' => 'Psl/Str/Byte/compare.php',
        'Psl\\Str\\Byte\\compare_ci' => 'Psl/Str/Byte/compare_ci.php',
        'Psl\\Str\\Byte\\contains' => 'Psl/Str/Byte/contains.php',
        'Psl\\Str\\Byte\\contains_ci' => 'Psl/Str/Byte/contains_ci.php',
        'Psl\\Str\\Byte\\ends_with' => 'Psl/Str/Byte/ends_with.php',
        'Psl\\Str\\Byte\\ends_with_ci' => 'Psl/Str/Byte/ends_with_ci.php',
        'Psl\\Str\\Byte\\length' => 'Psl/Str/Byte/length.php',
        'Psl\\Str\\Byte\\lowercase' => 'Psl/Str/Byte/lowercase.php',
        'Psl\\Str\\Byte\\ord' => 'Psl/Str/Byte/ord.php',
        'Psl\\Str\\Byte\\pad_left' => 'Psl/Str/Byte/pad_left.php',
        'Psl\\Str\\Byte\\pad_right' => 'Psl/Str/Byte/pad_right.php',
        'Psl\\Str\\Byte\\replace' => 'Psl/Str/Byte/replace.php',
        'Psl\\Str\\Byte\\replace_ci' => 'Psl/Str/Byte/replace_ci.php',
        'Psl\\Str\\Byte\\replace_every' => 'Psl/Str/Byte/replace_every.php',
        'Psl\\Str\\Byte\\replace_every_ci' => 'Psl/Str/Byte/replace_every_ci.php',
        'Psl\\Str\\Byte\\reverse' => 'Psl/Str/Byte/reverse.php',
        'Psl\\Str\\Byte\\rot13' => 'Psl/Str/Byte/rot13.php',
        'Psl\\Str\\Byte\\search' => 'Psl/Str/Byte/search.php',
        'Psl\\Str\\Byte\\search_ci' => 'Psl/Str/Byte/search_ci.php',
        'Psl\\Str\\Byte\\search_last' => 'Psl/Str/Byte/search_last.php',
        'Psl\\Str\\Byte\\search_last_ci' => 'Psl/Str/Byte/search_last_ci.php',
        'Psl\\Str\\Byte\\shuffle' => 'Psl/Str/Byte/shuffle.php',
        'Psl\\Str\\Byte\\slice' => 'Psl/Str/Byte/slice.php',
        'Psl\\Str\\Byte\\splice' => 'Psl/Str/Byte/splice.php',
        'Psl\\Str\\Byte\\split' => 'Psl/Str/Byte/split.php',
        'Psl\\Str\\Byte\\starts_with' => 'Psl/Str/Byte/starts_with.php',
        'Psl\\Str\\Byte\\starts_with_ci' => 'Psl/Str/Byte/starts_with_ci.php',
        'Psl\\Str\\Byte\\strip_prefix' => 'Psl/Str/Byte/strip_prefix.php',
        'Psl\\Str\\Byte\\strip_suffix' => 'Psl/Str/Byte/strip_suffix.php',
        'Psl\\Str\\Byte\\trim' => 'Psl/Str/Byte/trim.php',
        'Psl\\Str\\Byte\\trim_left' => 'Psl/Str/Byte/trim_left.php',
        'Psl\\Str\\Byte\\trim_right' => 'Psl/Str/Byte/trim_right.php',
        'Psl\\Str\\Byte\\uppercase' => 'Psl/Str/Byte/uppercase.php',
        'Psl\\Str\\Byte\\words' => 'Psl/Str/Byte/words.php',
        'Psl\\Str\\Byte\\wrap' => 'Psl/Str/Byte/wrap.php',
        'Psl\\Str\\Byte\\after' => 'Psl/Str/Byte/after.php',
        'Psl\\Str\\Byte\\after_ci' => 'Psl/Str/Byte/after_ci.php',
        'Psl\\Str\\Byte\\after_last' => 'Psl/Str/Byte/after_last.php',
        'Psl\\Str\\Byte\\after_last_ci' => 'Psl/Str/Byte/after_last_ci.php',
        'Psl\\Str\\Byte\\before' => 'Psl/Str/Byte/before.php',
        'Psl\\Str\\Byte\\before_ci' => 'Psl/Str/Byte/before_ci.php',
        'Psl\\Str\\Byte\\before_last' => 'Psl/Str/Byte/before_last.php',
        'Psl\\Str\\Byte\\before_last_ci' => 'Psl/Str/Byte/before_last_ci.php',
        'Psl\\Str\\capitalize' => 'Psl/Str/capitalize.php',
        'Psl\\Str\\capitalize_words' => 'Psl/Str/capitalize_words.php',
        'Psl\\Str\\chr' => 'Psl/Str/chr.php',
        'Psl\\Str\\chunk' => 'Psl/Str/chunk.php',
        'Psl\\Str\\concat' => 'Psl/Str/concat.php',
        'Psl\\Str\\contains' => 'Psl/Str/contains.php',
        'Psl\\Str\\contains_ci' => 'Psl/Str/contains_ci.php',
        'Psl\\Str\\detect_encoding' => 'Psl/Str/detect_encoding.php',
        'Psl\\Str\\convert_encoding' => 'Psl/Str/convert_encoding.php',
        'Psl\\Str\\is_utf8' => 'Psl/Str/is_utf8.php',
        'Psl\\Str\\ends_with' => 'Psl/Str/ends_with.php',
        'Psl\\Str\\ends_with_ci' => 'Psl/Str/ends_with_ci.php',
        'Psl\\Str\\fold' => 'Psl/Str/fold.php',
        'Psl\\Str\\format' => 'Psl/Str/format.php',
        'Psl\\Str\\format_number' => 'Psl/Str/format_number.php',
        'Psl\\Str\\from_code_points' => 'Psl/Str/from_code_points.php',
        'Psl\\Str\\is_empty' => 'Psl/Str/is_empty.php',
        'Psl\\Str\\join' => 'Psl/Str/join.php',
        'Psl\\Str\\length' => 'Psl/Str/length.php',
        'Psl\\Str\\levenshtein' => 'Psl/Str/levenshtein.php',
        'Psl\\Str\\lowercase' => 'Psl/Str/lowercase.php',
        'Psl\\Str\\metaphone' => 'Psl/Str/metaphone.php',
        'Psl\\Str\\ord' => 'Psl/Str/ord.php',
        'Psl\\Str\\pad_left' => 'Psl/Str/pad_left.php',
        'Psl\\Str\\pad_right' => 'Psl/Str/pad_right.php',
        'Psl\\Str\\repeat' => 'Psl/Str/repeat.php',
        'Psl\\Str\\replace' => 'Psl/Str/replace.php',
        'Psl\\Str\\replace_ci' => 'Psl/Str/replace_ci.php',
        'Psl\\Str\\replace_every' => 'Psl/Str/replace_every.php',
        'Psl\\Str\\replace_every_ci' => 'Psl/Str/replace_every_ci.php',
        'Psl\\Str\\reverse' => 'Psl/Str/reverse.php',
        'Psl\\Str\\search' => 'Psl/Str/search.php',
        'Psl\\Str\\search_ci' => 'Psl/Str/search_ci.php',
        'Psl\\Str\\search_last' => 'Psl/Str/search_last.php',
        'Psl\\Str\\search_last_ci' => 'Psl/Str/search_last_ci.php',
        'Psl\\Str\\slice' => 'Psl/Str/slice.php',
        'Psl\\Str\\splice' => 'Psl/Str/splice.php',
        'Psl\\Str\\split' => 'Psl/Str/split.php',
        'Psl\\Str\\starts_with' => 'Psl/Str/starts_with.php',
        'Psl\\Str\\starts_with_ci' => 'Psl/Str/starts_with_ci.php',
        'Psl\\Str\\strip_prefix' => 'Psl/Str/strip_prefix.php',
        'Psl\\Str\\strip_suffix' => 'Psl/Str/strip_suffix.php',
        'Psl\\Str\\to_int' => 'Psl/Str/to_int.php',
        'Psl\\Str\\trim' => 'Psl/Str/trim.php',
        'Psl\\Str\\trim_left' => 'Psl/Str/trim_left.php',
        'Psl\\Str\\trim_right' => 'Psl/Str/trim_right.php',
        'Psl\\Str\\truncate' => 'Psl/Str/truncate.php',
        'Psl\\Str\\uppercase' => 'Psl/Str/uppercase.php',
        'Psl\\Str\\width' => 'Psl/Str/width.php',
        'Psl\\Str\\wrap' => 'Psl/Str/wrap.php',
        'Psl\\Str\\after' => 'Psl/Str/after.php',
        'Psl\\Str\\after_ci' => 'Psl/Str/after_ci.php',
        'Psl\\Str\\after_last' => 'Psl/Str/after_last.php',
        'Psl\\Str\\after_last_ci' => 'Psl/Str/after_last_ci.php',
        'Psl\\Str\\before' => 'Psl/Str/before.php',
        'Psl\\Str\\before_ci' => 'Psl/Str/before_ci.php',
        'Psl\\Str\\before_last' => 'Psl/Str/before_last.php',
        'Psl\\Str\\before_last_ci' => 'Psl/Str/before_last_ci.php',
        'Psl\\invariant' => 'Psl/invariant.php',
        'Psl\\invariant_violation' => 'Psl/invariant_violation.php',
        'Psl\\sequence' => 'Psl/sequence.php',
        'Psl\\Type\\map' => 'Psl/Type/map.php',
        'Psl\\Type\\mutable_map' => 'Psl/Type/mutable_map.php',
        'Psl\\Type\\vector' => 'Psl/Type/vector.php',
        'Psl\\Type\\mutable_vector' => 'Psl/Type/mutable_vector.php',
        'Psl\\Type\\array_key' => 'Psl/Type/array_key.php',
        'Psl\\Type\\bool' => 'Psl/Type/bool.php',
        'Psl\\Type\\float' => 'Psl/Type/float.php',
        'Psl\\Type\\int' => 'Psl/Type/int.php',
        'Psl\\Type\\intersection' => 'Psl/Type/intersection.php',
        'Psl\\Type\\iterable' => 'Psl/Type/iterable.php',
        'Psl\\Type\\mixed' => 'Psl/Type/mixed.php',
        'Psl\\Type\\mixed_dict' => 'Psl/Type/mixed_dict.php',
        'Psl\\Type\\mixed_vec' => 'Psl/Type/mixed_vec.php',
        'Psl\\Type\\null' => 'Psl/Type/null.php',
        'Psl\\Type\\nullable' => 'Psl/Type/nullable.php',
        'Psl\\Type\\optional' => 'Psl/Type/optional.php',
        'Psl\\Type\\positive_int' => 'Psl/Type/positive_int.php',
        'Psl\\Type\\num' => 'Psl/Type/num.php',
        'Psl\\Type\\object' => 'Psl/Type/object.php',
        'Psl\\Type\\instance_of' => 'Psl/Type/instance_of.php',
        'Psl\\Type\\resource' => 'Psl/Type/resource.php',
        'Psl\\Type\\string' => 'Psl/Type/string.php',
        'Psl\\Type\\non_empty_dict' => 'Psl/Type/non_empty_dict.php',
        'Psl\\Type\\non_empty_string' => 'Psl/Type/non_empty_string.php',
        'Psl\\Type\\non_empty_vec' => 'Psl/Type/non_empty_vec.php',
        'Psl\\Type\\scalar' => 'Psl/Type/scalar.php',
        'Psl\\Type\\shape' => 'Psl/Type/shape.php',
        'Psl\\Type\\union' => 'Psl/Type/union.php',
        'Psl\\Type\\vec' => 'Psl/Type/vec.php',
        'Psl\\Type\\dict' => 'Psl/Type/dict.php',
        'Psl\\Type\\is_nan' => 'Psl/Type/is_nan.php',
        'Psl\\Type\\literal_scalar' => 'Psl/Type/literal_scalar.php',
        'Psl\\Type\\backed_enum' => 'Psl/Type/backed_enum.php',
        'Psl\\Type\\unit_enum' => 'Psl/Type/unit_enum.php',
        'Psl\\Json\\encode' => 'Psl/Json/encode.php',
        'Psl\\Json\\decode' => 'Psl/Json/decode.php',
        'Psl\\Json\\typed' => 'Psl/Json/typed.php',
        'Psl\\Env\\args' => 'Psl/Env/args.php',
        'Psl\\Env\\current_dir' => 'Psl/Env/current_dir.php',
        'Psl\\Env\\current_exec' => 'Psl/Env/current_exec.php',
        'Psl\\Env\\get_var' => 'Psl/Env/get_var.php',
        'Psl\\Env\\get_vars' => 'Psl/Env/get_vars.php',
        'Psl\\Env\\join_paths' => 'Psl/Env/join_paths.php',
        'Psl\\Env\\remove_var' => 'Psl/Env/remove_var.php',
        'Psl\\Env\\set_current_dir' => 'Psl/Env/set_current_dir.php',
        'Psl\\Env\\set_var' => 'Psl/Env/set_var.php',
        'Psl\\Env\\split_paths' => 'Psl/Env/split_paths.php',
        'Psl\\Env\\temp_dir' => 'Psl/Env/temp_dir.php',
        'Psl\\Password\\get_information' => 'Psl/Password/get_information.php',
        'Psl\\Password\\hash' => 'Psl/Password/hash.php',
        'Psl\\Password\\needs_rehash' => 'Psl/Password/needs_rehash.php',
        'Psl\\Password\\verify' => 'Psl/Password/verify.php',
        'Psl\\Hash\\hash' => 'Psl/Hash/hash.php',
        'Psl\\Hash\\equals' => 'Psl/Hash/equals.php',
        'Psl\\Hash\\Hmac\\hash' => 'Psl/Hash/Hmac/hash.php',
        'Psl\\Str\\Grapheme\\contains' => 'Psl/Str/Grapheme/contains.php',
        'Psl\\Str\\Grapheme\\contains_ci' => 'Psl/Str/Grapheme/contains_ci.php',
        'Psl\\Str\\Grapheme\\ends_with' => 'Psl/Str/Grapheme/ends_with.php',
        'Psl\\Str\\Grapheme\\ends_with_ci' => 'Psl/Str/Grapheme/ends_with_ci.php',
        'Psl\\Str\\Grapheme\\length' => 'Psl/Str/Grapheme/length.php',
        'Psl\\Str\\Grapheme\\reverse' => 'Psl/Str/Grapheme/reverse.php',
        'Psl\\Str\\Grapheme\\search' => 'Psl/Str/Grapheme/search.php',
        'Psl\\Str\\Grapheme\\search_ci' => 'Psl/Str/Grapheme/search_ci.php',
        'Psl\\Str\\Grapheme\\search_last' => 'Psl/Str/Grapheme/search_last.php',
        'Psl\\Str\\Grapheme\\search_last_ci' => 'Psl/Str/Grapheme/search_last_ci.php',
        'Psl\\Str\\Grapheme\\slice' => 'Psl/Str/Grapheme/slice.php',
        'Psl\\Str\\Grapheme\\starts_with' => 'Psl/Str/Grapheme/starts_with.php',
        'Psl\\Str\\Grapheme\\starts_with_ci' => 'Psl/Str/Grapheme/starts_with_ci.php',
        'Psl\\Str\\Grapheme\\strip_prefix' => 'Psl/Str/Grapheme/strip_prefix.php',
        'Psl\\Str\\Grapheme\\strip_suffix' => 'Psl/Str/Grapheme/strip_suffix.php',
        'Psl\\Str\\Grapheme\\after' => 'Psl/Str/Grapheme/after.php',
        'Psl\\Str\\Grapheme\\after_ci' => 'Psl/Str/Grapheme/after_ci.php',
        'Psl\\Str\\Grapheme\\after_last' => 'Psl/Str/Grapheme/after_last.php',
        'Psl\\Str\\Grapheme\\after_last_ci' => 'Psl/Str/Grapheme/after_last_ci.php',
        'Psl\\Str\\Grapheme\\before' => 'Psl/Str/Grapheme/before.php',
        'Psl\\Str\\Grapheme\\before_ci' => 'Psl/Str/Grapheme/before_ci.php',
        'Psl\\Str\\Grapheme\\before_last' => 'Psl/Str/Grapheme/before_last.php',
        'Psl\\Str\\Grapheme\\before_last_ci' => 'Psl/Str/Grapheme/before_last_ci.php',
        'Psl\\Encoding\\Base64\\encode' => 'Psl/Encoding/Base64/encode.php',
        'Psl\\Encoding\\Base64\\decode' => 'Psl/Encoding/Base64/decode.php',
        'Psl\\Encoding\\Hex\\encode' => 'Psl/Encoding/Hex/encode.php',
        'Psl\\Encoding\\Hex\\decode' => 'Psl/Encoding/Hex/decode.php',
        'Psl\\Shell\\execute' => 'Psl/Shell/execute.php',
        'Psl\\Shell\\unpack' => 'Psl/Shell/unpack.php',
        'Psl\\Shell\\stream_unpack' => 'Psl/Shell/stream_unpack.php',
        'Psl\\Shell\\Internal\\escape_argument' => 'Psl/Shell/Internal/escape_argument.php',
        'Psl\\Html\\encode' => 'Psl/Html/encode.php',
        'Psl\\Html\\encode_special_characters' => 'Psl/Html/encode_special_characters.php',
        'Psl\\Html\\decode' => 'Psl/Html/decode.php',
        'Psl\\Html\\decode_special_characters' => 'Psl/Html/decode_special_characters.php',
        'Psl\\Html\\strip_tags' => 'Psl/Html/strip_tags.php',
        'Psl\\Filesystem\\change_group' => 'Psl/Filesystem/change_group.php',
        'Psl\\Filesystem\\change_owner' => 'Psl/Filesystem/change_owner.php',
        'Psl\\Filesystem\\change_permissions' => 'Psl/Filesystem/change_permissions.php',
        'Psl\\Filesystem\\copy' => 'Psl/Filesystem/copy.php',
        'Psl\\Filesystem\\create_directory' => 'Psl/Filesystem/create_directory.php',
        'Psl\\Filesystem\\create_file' => 'Psl/Filesystem/create_file.php',
        'Psl\\Filesystem\\delete_directory' => 'Psl/Filesystem/delete_directory.php',
        'Psl\\Filesystem\\delete_file' => 'Psl/Filesystem/delete_file.php',
        'Psl\\Filesystem\\exists' => 'Psl/Filesystem/exists.php',
        'Psl\\Filesystem\\file_size' => 'Psl/Filesystem/file_size.php',
        'Psl\\Filesystem\\get_group' => 'Psl/Filesystem/get_group.php',
        'Psl\\Filesystem\\get_owner' => 'Psl/Filesystem/get_owner.php',
        'Psl\\Filesystem\\get_permissions' => 'Psl/Filesystem/get_permissions.php',
        'Psl\\Filesystem\\get_basename' => 'Psl/Filesystem/get_basename.php',
        'Psl\\Filesystem\\get_directory' => 'Psl/Filesystem/get_directory.php',
        'Psl\\Filesystem\\get_extension' => 'Psl/Filesystem/get_extension.php',
        'Psl\\Filesystem\\get_filename' => 'Psl/Filesystem/get_filename.php',
        'Psl\\Filesystem\\is_directory' => 'Psl/Filesystem/is_directory.php',
        'Psl\\Filesystem\\is_file' => 'Psl/Filesystem/is_file.php',
        'Psl\\Filesystem\\is_symbolic_link' => 'Psl/Filesystem/is_symbolic_link.php',
        'Psl\\Filesystem\\is_readable' => 'Psl/Filesystem/is_readable.php',
        'Psl\\Filesystem\\is_writable' => 'Psl/Filesystem/is_writable.php',
        'Psl\\Filesystem\\canonicalize' => 'Psl/Filesystem/canonicalize.php',
        'Psl\\Filesystem\\is_executable' => 'Psl/Filesystem/is_executable.php',
        'Psl\\Filesystem\\read_directory' => 'Psl/Filesystem/read_directory.php',
        'Psl\\File\\read' => 'Psl/File/read.php',
        'Psl\\Filesystem\\read_symbolic_link' => 'Psl/Filesystem/read_symbolic_link.php',
        'Psl\\File\\write' => 'Psl/File/write.php',
        'Psl\\Filesystem\\create_temporary_file' => 'Psl/Filesystem/create_temporary_file.php',
        'Psl\\Filesystem\\create_hard_link' => 'Psl/Filesystem/create_hard_link.php',
        'Psl\\Filesystem\\create_symbolic_link' => 'Psl/Filesystem/create_symbolic_link.php',
        'Psl\\Filesystem\\get_access_time' => 'Psl/Filesystem/get_access_time.php',
        'Psl\\Filesystem\\get_change_time' => 'Psl/Filesystem/get_change_time.php',
        'Psl\\Filesystem\\get_modification_time' => 'Psl/Filesystem/get_modification_time.php',
        'Psl\\Filesystem\\get_inode' => 'Psl/Filesystem/get_inode.php',
        'Psl\\IO\\Internal\\open_resource' => 'Psl/IO/Internal/open_resource.php',
        'Psl\\IO\\input_handle' => 'Psl/IO/input_handle.php',
        'Psl\\IO\\output_handle' => 'Psl/IO/output_handle.php',
        'Psl\\IO\\error_handle' => 'Psl/IO/error_handle.php',
        'Psl\\IO\\pipe' => 'Psl/IO/pipe.php',
        'Psl\\Class\\exists' => 'Psl/Class/exists.php',
        'Psl\\Class\\defined' => 'Psl/Class/defined.php',
        'Psl\\Class\\has_constant' => 'Psl/Class/has_constant.php',
        'Psl\\Class\\has_method' => 'Psl/Class/has_method.php',
        'Psl\\Class\\is_abstract' => 'Psl/Class/is_abstract.php',
        'Psl\\Class\\is_final' => 'Psl/Class/is_final.php',
        'Psl\\Interface\\exists' => 'Psl/Interface/exists.php',
        'Psl\\Interface\\defined' => 'Psl/Interface/defined.php',
        'Psl\\Trait\\exists' => 'Psl/Trait/exists.php',
        'Psl\\Trait\\defined' => 'Psl/Trait/defined.php',
        'Psl\\Async\\main' => 'Psl/Async/main.php',
        'Psl\\Async\\run' => 'Psl/Async/run.php',
        'Psl\\Async\\concurrently' => 'Psl/Async/concurrently.php',
        'Psl\\Result\\reflect' => 'Psl/Result/reflect.php',
        'Psl\\Async\\series' => 'Psl/Async/series.php',
        'Psl\\Async\\await' => 'Psl/Async/await.php',
        'Psl\\Async\\any' => 'Psl/Async/any.php',
        'Psl\\Async\\all' => 'Psl/Async/all.php',
        'Psl\\Async\\first' => 'Psl/Async/first.php',
        'Psl\\Async\\later' => 'Psl/Async/later.php',
        'Psl\\Async\\sleep' => 'Psl/Async/sleep.php',
        'Psl\\File\\Internal\\open' => 'Psl/File/Internal/open.php',
        'Psl\\File\\open_read_only' => 'Psl/File/open_read_only.php',
        'Psl\\File\\open_write_only' => 'Psl/File/open_write_only.php',
        'Psl\\File\\open_read_write' => 'Psl/File/open_read_write.php',
        'Psl\\Runtime\\get_extensions' => 'Psl/Runtime/get_extensions.php',
        'Psl\\Runtime\\get_sapi' => 'Psl/Runtime/get_sapi.php',
        'Psl\\Runtime\\get_version' => 'Psl/Runtime/get_version.php',
        'Psl\\Runtime\\get_version_id' => 'Psl/Runtime/get_version_id.php',
        'Psl\\Runtime\\get_version_details' => 'Psl/Runtime/get_version_details.php',
        'Psl\\Runtime\\get_zend_version' => 'Psl/Runtime/get_zend_version.php',
        'Psl\\Runtime\\get_zend_extensions' => 'Psl/Runtime/get_zend_extensions.php',
        'Psl\\Runtime\\has_extension' => 'Psl/Runtime/has_extension.php',
        'Psl\\Runtime\\is_debug' => 'Psl/Runtime/is_debug.php',
        'Psl\\Runtime\\is_thread_safe' => 'Psl/Runtime/is_thread_safe.php',
        'Psl\\Network\\Internal\\get_peer_name' => 'Psl/Network/Internal/get_peer_name.php',
        'Psl\\Network\\Internal\\get_sock_name' => 'Psl/Network/Internal/get_sock_name.php',
        'Psl\\Network\\Internal\\socket_connect' => 'Psl/Network/Internal/socket_connect.php',
        'Psl\\Network\\Internal\\server_listen' => 'Psl/Network/Internal/server_listen.php',
        'Psl\\TCP\\connect' => 'Psl/TCP/connect.php',
        'Psl\\Unix\\connect' => 'Psl/Unix/connect.php',
        'Psl\\Channel\\bounded' => 'Psl/Channel/bounded.php',
        'Psl\\Channel\\unbounded' => 'Psl/Channel/unbounded.php',
        'Psl\\IO\\streaming' => 'Psl/IO/streaming.php',
        'Psl\\IO\\write' => 'Psl/IO/write.php',
        'Psl\\IO\\write_line' => 'Psl/IO/write_line.php',
        'Psl\\IO\\write_error' => 'Psl/IO/write_error.php',
        'Psl\\IO\\write_error_line' => 'Psl/IO/write_error_line.php',
        'Psl\\OS\\family' => 'Psl/OS/family.php',
        'Psl\\OS\\is_windows' => 'Psl/OS/is_windows.php',
        'Psl\\OS\\is_darwin' => 'Psl/OS/is_darwin.php',
        'Psl\\Option\\some' => 'Psl/Option/some.php',
        'Psl\\Option\\none' => 'Psl/Option/none.php',
    ];

    public const INTERFACES = [
        'Psl\\DataStructure\\PriorityQueueInterface' => 'Psl/DataStructure/PriorityQueueInterface.php',
        'Psl\\DataStructure\\QueueInterface' => 'Psl/DataStructure/QueueInterface.php',
        'Psl\\DataStructure\\StackInterface' => 'Psl/DataStructure/StackInterface.php',
        'Psl\\Exception\\ExceptionInterface' => 'Psl/Exception/ExceptionInterface.php',
        'Psl\\Collection\\CollectionInterface' => 'Psl/Collection/CollectionInterface.php',
        'Psl\\Collection\\IndexAccessInterface' => 'Psl/Collection/IndexAccessInterface.php',
        'Psl\\Collection\\MutableCollectionInterface' => 'Psl/Collection/MutableCollectionInterface.php',
        'Psl\\Collection\\MutableIndexAccessInterface' => 'Psl/Collection/MutableIndexAccessInterface.php',
        'Psl\\Collection\\AccessibleCollectionInterface' => 'Psl/Collection/AccessibleCollectionInterface.php',
        'Psl\\Collection\\MutableAccessibleCollectionInterface' => 'Psl/Collection/MutableAccessibleCollectionInterface.php',
        'Psl\\Collection\\VectorInterface' => 'Psl/Collection/VectorInterface.php',
        'Psl\\Collection\\MutableVectorInterface' => 'Psl/Collection/MutableVectorInterface.php',
        'Psl\\Collection\\MapInterface' => 'Psl/Collection/MapInterface.php',
        'Psl\\Collection\\MutableMapInterface' => 'Psl/Collection/MutableMapInterface.php',
        'Psl\\Observer\\SubjectInterface' => 'Psl/Observer/SubjectInterface.php',
        'Psl\\Observer\\ObserverInterface' => 'Psl/Observer/ObserverInterface.php',
        'Psl\\Result\\ResultInterface' => 'Psl/Result/ResultInterface.php',
        'Psl\\Math\\Exception\\ExceptionInterface' => 'Psl/Math/Exception/ExceptionInterface.php',
        'Psl\\Encoding\\Exception\\ExceptionInterface' => 'Psl/Encoding/Exception/ExceptionInterface.php',
        'Psl\\Type\\TypeInterface' => 'Psl/Type/TypeInterface.php',
        'Psl\\Type\\Exception\\ExceptionInterface' => 'Psl/Type/Exception/ExceptionInterface.php',
        'Psl\\Regex\\Exception\\ExceptionInterface' => 'Psl/Regex/Exception/ExceptionInterface.php',
        'Psl\\SecureRandom\\Exception\\ExceptionInterface' => 'Psl/SecureRandom/Exception/ExceptionInterface.php',
        'Psl\\Shell\\Exception\\ExceptionInterface' => 'Psl/Shell/Exception/ExceptionInterface.php',
        'Psl\\Filesystem\\Exception\\ExceptionInterface' => 'Psl/Filesystem/Exception/ExceptionInterface.php',
        'Psl\\IO\\Exception\\ExceptionInterface' => 'Psl/IO/Exception/ExceptionInterface.php',
        'Psl\\IO\\CloseHandleInterface' => 'Psl/IO/CloseHandleInterface.php',
        'Psl\\IO\\CloseReadHandleInterface' => 'Psl/IO/CloseReadHandleInterface.php',
        'Psl\\IO\\CloseReadWriteHandleInterface' => 'Psl/IO/CloseReadWriteHandleInterface.php',
        'Psl\\IO\\CloseSeekHandleInterface' => 'Psl/IO/CloseSeekHandleInterface.php',
        'Psl\\IO\\CloseSeekReadHandleInterface' => 'Psl/IO/CloseSeekReadHandleInterface.php',
        'Psl\\IO\\CloseSeekReadWriteHandleInterface' => 'Psl/IO/CloseSeekReadWriteHandleInterface.php',
        'Psl\\IO\\CloseSeekWriteHandleInterface' => 'Psl/IO/CloseSeekWriteHandleInterface.php',
        'Psl\\IO\\CloseWriteHandleInterface' => 'Psl/IO/CloseWriteHandleInterface.php',
        'Psl\\IO\\HandleInterface' => 'Psl/IO/HandleInterface.php',
        'Psl\\IO\\ReadHandleInterface' => 'Psl/IO/ReadHandleInterface.php',
        'Psl\\IO\\ReadWriteHandleInterface' => 'Psl/IO/ReadWriteHandleInterface.php',
        'Psl\\IO\\SeekHandleInterface' => 'Psl/IO/SeekHandleInterface.php',
        'Psl\\IO\\SeekReadHandleInterface' => 'Psl/IO/SeekReadHandleInterface.php',
        'Psl\\IO\\SeekReadWriteHandleInterface' => 'Psl/IO/SeekReadWriteHandleInterface.php',
        'Psl\\IO\\SeekWriteHandleInterface' => 'Psl/IO/SeekWriteHandleInterface.php',
        'Psl\\IO\\WriteHandleInterface' => 'Psl/IO/WriteHandleInterface.php',
        'Psl\\IO\\CloseStreamHandleInterface' => 'Psl/IO/CloseStreamHandleInterface.php',
        'Psl\\IO\\CloseReadStreamHandleInterface' => 'Psl/IO/CloseReadStreamHandleInterface.php',
        'Psl\\IO\\CloseReadWriteStreamHandleInterface' => 'Psl/IO/CloseReadWriteStreamHandleInterface.php',
        'Psl\\IO\\CloseSeekStreamHandleInterface' => 'Psl/IO/CloseSeekStreamHandleInterface.php',
        'Psl\\IO\\CloseSeekReadStreamHandleInterface' => 'Psl/IO/CloseSeekReadStreamHandleInterface.php',
        'Psl\\IO\\CloseSeekReadWriteStreamHandleInterface' => 'Psl/IO/CloseSeekReadWriteStreamHandleInterface.php',
        'Psl\\IO\\CloseSeekWriteStreamHandleInterface' => 'Psl/IO/CloseSeekWriteStreamHandleInterface.php',
        'Psl\\IO\\CloseWriteStreamHandleInterface' => 'Psl/IO/CloseWriteStreamHandleInterface.php',
        'Psl\\IO\\StreamHandleInterface' => 'Psl/IO/StreamHandleInterface.php',
        'Psl\\IO\\ReadStreamHandleInterface' => 'Psl/IO/ReadStreamHandleInterface.php',
        'Psl\\IO\\ReadWriteStreamHandleInterface' => 'Psl/IO/ReadWriteStreamHandleInterface.php',
        'Psl\\IO\\SeekStreamHandleInterface' => 'Psl/IO/SeekStreamHandleInterface.php',
        'Psl\\IO\\SeekReadStreamHandleInterface' => 'Psl/IO/SeekReadStreamHandleInterface.php',
        'Psl\\IO\\SeekReadWriteStreamHandleInterface' => 'Psl/IO/SeekReadWriteStreamHandleInterface.php',
        'Psl\\IO\\SeekWriteStreamHandleInterface' => 'Psl/IO/SeekWriteStreamHandleInterface.php',
        'Psl\\IO\\WriteStreamHandleInterface' => 'Psl/IO/WriteStreamHandleInterface.php',
        'Psl\\RandomSequence\\SequenceInterface' => 'Psl/RandomSequence/SequenceInterface.php',
        'Psl\\Async\\Exception\\ExceptionInterface' => 'Psl/Async/Exception/ExceptionInterface.php',
        'Psl\\File\\Exception\\ExceptionInterface' => 'Psl/File/Exception/ExceptionInterface.php',
        'Psl\\File\\HandleInterface' => 'Psl/File/HandleInterface.php',
        'Psl\\File\\ReadHandleInterface' => 'Psl/File/ReadHandleInterface.php',
        'Psl\\File\\WriteHandleInterface' => 'Psl/File/WriteHandleInterface.php',
        'Psl\\File\\ReadWriteHandleInterface' => 'Psl/File/ReadWriteHandleInterface.php',
        'Psl\\Network\\Exception\\ExceptionInterface' => 'Psl/Network/Exception/ExceptionInterface.php',
        'Psl\\Network\\SocketInterface' => 'Psl/Network/SocketInterface.php',
        'Psl\\Network\\StreamSocketInterface' => 'Psl/Network/StreamSocketInterface.php',
        'Psl\\Network\\ServerInterface' => 'Psl/Network/ServerInterface.php',
        'Psl\\Network\\StreamServerInterface' => 'Psl/Network/StreamServerInterface.php',
        'Psl\\Channel\\ChannelInterface' => 'Psl/Channel/ChannelInterface.php',
        'Psl\\Channel\\SenderInterface' => 'Psl/Channel/SenderInterface.php',
        'Psl\\Channel\\ReceiverInterface' => 'Psl/Channel/ReceiverInterface.php',
        'Psl\\Channel\\Exception\\ExceptionInterface' => 'Psl/Channel/Exception/ExceptionInterface.php',
        'Psl\\Promise\\PromiseInterface' => 'Psl/Promise/PromiseInterface.php',
        'Psl\\Iter\\Exception\\ExceptionInterface' => 'Psl/Iter/Exception/ExceptionInterface.php',
        'Psl\\Str\\Exception\\ExceptionInterface' => 'Psl/Str/Exception/ExceptionInterface.php',
        'Psl\\Collection\\Exception\\ExceptionInterface' => 'Psl/Collection/Exception/ExceptionInterface.php',
        'Psl\\DataStructure\\Exception\\ExceptionInterface' => 'Psl/DataStructure/Exception/ExceptionInterface.php',
        'Psl\\Vec\\Exception\\ExceptionInterface' => 'Psl/Vec/Exception/ExceptionInterface.php',
        'Psl\\Dict\\Exception\\ExceptionInterface' => 'Psl/Dict/Exception/ExceptionInterface.php',
        'Psl\\PseudoRandom\\Exception\\ExceptionInterface' => 'Psl/PseudoRandom/Exception/ExceptionInterface.php',
        'Psl\\Option\\Exception\\ExceptionInterface' => 'Psl/Option/Exception/ExceptionInterface.php',
    ];

    public const TRAITS = [
        'Psl\\RandomSequence\\Internal\\MersenneTwisterTrait' => 'Psl/RandomSequence/Internal/MersenneTwisterTrait.php',
        'Psl\\IO\\ReadHandleConvenienceMethodsTrait' => 'Psl/IO/ReadHandleConvenienceMethodsTrait.php',
        'Psl\\IO\\WriteHandleConvenienceMethodsTrait' => 'Psl/IO/WriteHandleConvenienceMethodsTrait.php',
        'Psl\\Channel\\Internal\\ChannelSideTrait' => 'Psl/Channel/Internal/ChannelSideTrait.php',
    ];

    public const CLASSES = [
        'Psl\\Ref' => 'Psl/Ref.php',
        'Psl\\DataStructure\\PriorityQueue' => 'Psl/DataStructure/PriorityQueue.php',
        'Psl\\DataStructure\\Queue' => 'Psl/DataStructure/Queue.php',
        'Psl\\DataStructure\\Stack' => 'Psl/DataStructure/Stack.php',
        'Psl\\Iter\\Iterator' => 'Psl/Iter/Iterator.php',
        'Psl\\Collection\\Vector' => 'Psl/Collection/Vector.php',
        'Psl\\Collection\\MutableVector' => 'Psl/Collection/MutableVector.php',
        'Psl\\Collection\\Map' => 'Psl/Collection/Map.php',
        'Psl\\Collection\\MutableMap' => 'Psl/Collection/MutableMap.php',
        'Psl\\Exception\\OverflowException' => 'Psl/Exception/OverflowException.php',
        'Psl\\Exception\\InvalidArgumentException' => 'Psl/Exception/InvalidArgumentException.php',
        'Psl\\Exception\\RuntimeException' => 'Psl/Exception/RuntimeException.php',
        'Psl\\Exception\\InvariantViolationException' => 'Psl/Exception/InvariantViolationException.php',
        'Psl\\Exception\\UnderflowException' => 'Psl/Exception/UnderflowException.php',
        'Psl\\Exception\\OutOfBoundsException' => 'Psl/Exception/OutOfBoundsException.php',
        'Psl\\Exception\\LogicException' => 'Psl/Exception/LogicException.php',
        'Psl\\Result\\Failure' => 'Psl/Result/Failure.php',
        'Psl\\Result\\Stats' => 'Psl/Result/Stats.php',
        'Psl\\Result\\Success' => 'Psl/Result/Success.php',
        'Psl\\Type\\Internal\\ArrayKeyType' => 'Psl/Type/Internal/ArrayKeyType.php',
        'Psl\\Type\\Internal\\MapType' => 'Psl/Type/Internal/MapType.php',
        'Psl\\Type\\Internal\\MutableMapType' => 'Psl/Type/Internal/MutableMapType.php',
        'Psl\\Type\\Internal\\VectorType' => 'Psl/Type/Internal/VectorType.php',
        'Psl\\Type\\Internal\\MutableVectorType' => 'Psl/Type/Internal/MutableVectorType.php',
        'Psl\\Type\\Internal\\BoolType' => 'Psl/Type/Internal/BoolType.php',
        'Psl\\Type\\Internal\\FloatType' => 'Psl/Type/Internal/FloatType.php',
        'Psl\\Type\\Internal\\IntersectionType' => 'Psl/Type/Internal/IntersectionType.php',
        'Psl\\Type\\Internal\\IntType' => 'Psl/Type/Internal/IntType.php',
        'Psl\\Type\\Internal\\IterableType' => 'Psl/Type/Internal/IterableType.php',
        'Psl\\Type\\Internal\\MixedType' => 'Psl/Type/Internal/MixedType.php',
        'Psl\\Type\\Internal\\NullType' => 'Psl/Type/Internal/NullType.php',
        'Psl\\Type\\Internal\\NullableType' => 'Psl/Type/Internal/NullableType.php',
        'Psl\\Type\\Internal\\OptionalType' => 'Psl/Type/Internal/OptionalType.php',
        'Psl\\Type\\Internal\\PositiveIntType' => 'Psl/Type/Internal/PositiveIntType.php',
        'Psl\\Type\\Internal\\NumType' => 'Psl/Type/Internal/NumType.php',
        'Psl\\Type\\Internal\\ObjectType' => 'Psl/Type/Internal/ObjectType.php',
        'Psl\\Type\\Internal\\InstanceOfType' => 'Psl/Type/Internal/InstanceOfType.php',
        'Psl\\Type\\Internal\\ResourceType' => 'Psl/Type/Internal/ResourceType.php',
        'Psl\\Type\\Internal\\StringType' => 'Psl/Type/Internal/StringType.php',
        'Psl\\Type\\Internal\\ShapeType' => 'Psl/Type/Internal/ShapeType.php',
        'Psl\\Type\\Internal\\NonEmptyDictType' => 'Psl/Type/Internal/NonEmptyDictType.php',
        'Psl\\Type\\Internal\\NonEmptyStringType' => 'Psl/Type/Internal/NonEmptyStringType.php',
        'Psl\\Type\\Internal\\NonEmptyVecType' => 'Psl/Type/Internal/NonEmptyVecType.php',
        'Psl\\Type\\Internal\\UnionType' => 'Psl/Type/Internal/UnionType.php',
        'Psl\\Type\\Internal\\VecType' => 'Psl/Type/Internal/VecType.php',
        'Psl\\Type\\Internal\\DictType' => 'Psl/Type/Internal/DictType.php',
        'Psl\\Type\\Internal\\ScalarType' => 'Psl/Type/Internal/ScalarType.php',
        'Psl\\Type\\Internal\\LiteralScalarType' => 'Psl/Type/Internal/LiteralScalarType.php',
        'Psl\\Type\\Internal\\BackedEnumType' => 'Psl/Type/Internal/BackedEnumType.php',
        'Psl\\Type\\Internal\\UnitEnumType' => 'Psl/Type/Internal/UnitEnumType.php',
        'Psl\\Type\\Exception\\TypeTrace' => 'Psl/Type/Exception/TypeTrace.php',
        'Psl\\Type\\Exception\\AssertException' => 'Psl/Type/Exception/AssertException.php',
        'Psl\\Type\\Exception\\CoercionException' => 'Psl/Type/Exception/CoercionException.php',
        'Psl\\Type\\Exception\\Exception' => 'Psl/Type/Exception/Exception.php',
        'Psl\\Type\\Type' => 'Psl/Type/Type.php',
        'Psl\\Json\\Exception\\ExceptionInterface' => 'Psl/Json/Exception/ExceptionInterface.php',
        'Psl\\Json\\Exception\\DecodeException' => 'Psl/Json/Exception/DecodeException.php',
        'Psl\\Json\\Exception\\EncodeException' => 'Psl/Json/Exception/EncodeException.php',
        'Psl\\Hash\\Exception\\ExceptionInterface' => 'Psl/Hash/Exception/ExceptionInterface.php',
        'Psl\\Hash\\Exception\\RuntimeException' => 'Psl/Hash/Exception/RuntimeException.php',
        'Psl\\Hash\\Context' => 'Psl/Hash/Context.php',
        'Psl\\Encoding\\Exception\\IncorrectPaddingException' => 'Psl/Encoding/Exception/IncorrectPaddingException.php',
        'Psl\\Encoding\\Exception\\RangeException' => 'Psl/Encoding/Exception/RangeException.php',
        'Psl\\SecureRandom\\Exception\\InsufficientEntropyException' => 'Psl/SecureRandom/Exception/InsufficientEntropyException.php',
        'Psl\\Regex\\Exception\\InvalidPatternException' => 'Psl/Regex/Exception/InvalidPatternException.php',
        'Psl\\Regex\\Exception\\RuntimeException' => 'Psl/Regex/Exception/RuntimeException.php',
        'Psl\\Shell\\Exception\\FailedExecutionException' => 'Psl/Shell/Exception/FailedExecutionException.php',
        'Psl\\Shell\\Exception\\RuntimeException' => 'Psl/Shell/Exception/RuntimeException.php',
        'Psl\\Shell\\Exception\\PossibleAttackException' => 'Psl/Shell/Exception/PossibleAttackException.php',
        'Psl\\Shell\\Exception\\TimeoutException' => 'Psl/Shell/Exception/TimeoutException.php',
        'Psl\\Shell\\Exception\\InvalidArgumentException' => 'Psl/Shell/Exception/InvalidArgumentException.php',
        'Psl\\Math\\Exception\\ArithmeticException' => 'Psl/Math/Exception/ArithmeticException.php',
        'Psl\\Math\\Exception\\DivisionByZeroException' => 'Psl/Math/Exception/DivisionByZeroException.php',
        'Psl\\Filesystem\\Exception\\RuntimeException' => 'Psl/Filesystem/Exception/RuntimeException.php',
        'Psl\\Filesystem\\Exception\\InvalidArgumentException' => 'Psl/Filesystem/Exception/InvalidArgumentException.php',
        'Psl\\Filesystem\\Exception\\NotFileException' => 'Psl/Filesystem/Exception/NotFileException.php',
        'Psl\\Filesystem\\Exception\\NotDirectoryException' => 'Psl/Filesystem/Exception/NotDirectoryException.php',
        'Psl\\Filesystem\\Exception\\NotFoundException' => 'Psl/Filesystem/Exception/NotFoundException.php',
        'Psl\\Filesystem\\Exception\\NotSymbolicLinkException' => 'Psl/Filesystem/Exception/NotSymbolicLinkException.php',
        'Psl\\Filesystem\\Exception\\NotReadableException' => 'Psl/Filesystem/Exception/NotReadableException.php',
        'Psl\\IO\\Exception\\AlreadyClosedException' => 'Psl/IO/Exception/AlreadyClosedException.php',
        'Psl\\IO\\Exception\\RuntimeException' => 'Psl/IO/Exception/RuntimeException.php',
        'Psl\\IO\\Exception\\TimeoutException' => 'Psl/IO/Exception/TimeoutException.php',
        'Psl\\IO\\Internal\\ResourceHandle' => 'Psl/IO/Internal/ResourceHandle.php',
        'Psl\\IO\\Reader' => 'Psl/IO/Reader.php',
        'Psl\\IO\\MemoryHandle' => 'Psl/IO/MemoryHandle.php',
        'Psl\\Fun\\Internal\\LazyEvaluator' => 'Psl/Fun/Internal/LazyEvaluator.php',
        'Psl\\RandomSequence\\MersenneTwisterSequence' => 'Psl/RandomSequence/MersenneTwisterSequence.php',
        'Psl\\RandomSequence\\MersenneTwisterPHPVariantSequence' => 'Psl/RandomSequence/MersenneTwisterPHPVariantSequence.php',
        'Psl\\RandomSequence\\SecureSequence' => 'Psl/RandomSequence/SecureSequence.php',
        'Psl\\Async\\Exception\\CompositeException' => 'Psl/Async/Exception/CompositeException.php',
        'Psl\\Async\\Exception\\RuntimeException' => 'Psl/Async/Exception/RuntimeException.php',
        'Psl\\Async\\Exception\\TimeoutException' => 'Psl/Async/Exception/TimeoutException.php',
        'Psl\\Async\\Exception\\UnhandledAwaitableException' => 'Psl/Async/Exception/UnhandledAwaitableException.php',
        'Psl\\Async\\Exception\\ResourceClosedException' => 'Psl/Async/Exception/ResourceClosedException.php',
        'Psl\\Async\\Internal\\AwaitableIterator' => 'Psl/Async/Internal/AwaitableIterator.php',
        'Psl\\Async\\Internal\\AwaitableIteratorQueue' => 'Psl/Async/Internal/AwaitableIteratorQueue.php',
        'Psl\\Async\\Internal\\State' => 'Psl/Async/Internal/State.php',
        'Psl\\Async\\Awaitable' => 'Psl/Async/Awaitable.php',
        'Psl\\Async\\Semaphore' => 'Psl/Async/Semaphore.php',
        'Psl\\Async\\KeyedSemaphore' => 'Psl/Async/KeyedSemaphore.php',
        'Psl\\Async\\Sequence' => 'Psl/Async/Sequence.php',
        'Psl\\Async\\KeyedSequence' => 'Psl/Async/KeyedSequence.php',
        'Psl\\Async\\Deferred' => 'Psl/Async/Deferred.php',
        'Psl\\Async\\Scheduler' => 'Psl/Async/Scheduler.php',
        'Psl\\IO\\CloseStreamHandle' => 'Psl/IO/CloseStreamHandle.php',
        'Psl\\IO\\CloseReadStreamHandle' => 'Psl/IO/CloseReadStreamHandle.php',
        'Psl\\IO\\CloseReadWriteStreamHandle' => 'Psl/IO/CloseReadWriteStreamHandle.php',
        'Psl\\IO\\CloseSeekStreamHandle' => 'Psl/IO/CloseSeekStreamHandle.php',
        'Psl\\IO\\CloseSeekReadStreamHandle' => 'Psl/IO/CloseSeekReadStreamHandle.php',
        'Psl\\IO\\CloseSeekReadWriteStreamHandle' => 'Psl/IO/CloseSeekReadWriteStreamHandle.php',
        'Psl\\IO\\CloseSeekWriteStreamHandle' => 'Psl/IO/CloseSeekWriteStreamHandle.php',
        'Psl\\IO\\CloseWriteStreamHandle' => 'Psl/IO/CloseWriteStreamHandle.php',
        'Psl\\IO\\ReadStreamHandle' => 'Psl/IO/ReadStreamHandle.php',
        'Psl\\IO\\ReadWriteStreamHandle' => 'Psl/IO/ReadWriteStreamHandle.php',
        'Psl\\IO\\SeekStreamHandle' => 'Psl/IO/SeekStreamHandle.php',
        'Psl\\IO\\SeekReadStreamHandle' => 'Psl/IO/SeekReadStreamHandle.php',
        'Psl\\IO\\SeekReadWriteStreamHandle' => 'Psl/IO/SeekReadWriteStreamHandle.php',
        'Psl\\IO\\SeekWriteStreamHandle' => 'Psl/IO/SeekWriteStreamHandle.php',
        'Psl\\IO\\WriteStreamHandle' => 'Psl/IO/WriteStreamHandle.php',
        'Psl\\IO\\Internal\\OptionalIncrementalTimeout' => 'Psl/IO/Internal/OptionalIncrementalTimeout.php',
        'Psl\\File\\Exception\\AlreadyLockedException' => 'Psl/File/Exception/AlreadyLockedException.php',
        'Psl\\File\\Exception\\RuntimeException' => 'Psl/File/Exception/RuntimeException.php',
        'Psl\\File\\Internal\\AbstractHandleWrapper' => 'Psl/File/Internal/AbstractHandleWrapper.php',
        'Psl\\File\\Internal\\ResourceHandle' => 'Psl/File/Internal/ResourceHandle.php',
        'Psl\\File\\Lock' => 'Psl/File/Lock.php',
        'Psl\\File\\ReadHandle' => 'Psl/File/ReadHandle.php',
        'Psl\\File\\ReadWriteHandle' => 'Psl/File/ReadWriteHandle.php',
        'Psl\\File\\WriteHandle' => 'Psl/File/WriteHandle.php',
        'Psl\\Network\\Exception\\TimeoutException' => 'Psl/Network/Exception/TimeoutException.php',
        'Psl\\Network\\Exception\\RuntimeException' => 'Psl/Network/Exception/RuntimeException.php',
        'Psl\\Network\\Exception\\AlreadyStoppedException' => 'Psl/Network/Exception/AlreadyStoppedException.php',
        'Psl\\Network\\Exception\\InvalidArgumentException' => 'Psl/Network/Exception/InvalidArgumentException.php',
        'Psl\\Network\\Address' => 'Psl/Network/Address.php',
        'Psl\\Network\\SocketOptions' => 'Psl/Network/SocketOptions.php',
        'Psl\\Network\\Internal\\AbstractStreamServer' => 'Psl/Network/Internal/AbstractStreamServer.php',
        'Psl\\Network\\Internal\\Socket' => 'Psl/Network/Internal/Socket.php',
        'Psl\\TCP\\ConnectOptions' => 'Psl/TCP/ConnectOptions.php',
        'Psl\\TCP\\ServerOptions' => 'Psl/TCP/ServerOptions.php',
        'Psl\\TCP\\Server' => 'Psl/TCP/Server.php',
        'Psl\\Unix\\Server' => 'Psl/Unix/Server.php',
        'Psl\\Channel\\Internal\\BoundedChannelState' => 'Psl/Channel/Internal/BoundedChannelState.php',
        'Psl\\Channel\\Internal\\BoundedSender' => 'Psl/Channel/Internal/BoundedSender.php',
        'Psl\\Channel\\Internal\\BoundedReceiver' => 'Psl/Channel/Internal/BoundedReceiver.php',
        'Psl\\Channel\\Internal\\UnboundedChannelState' => 'Psl/Channel/Internal/UnboundedChannelState.php',
        'Psl\\Channel\\Internal\\UnboundedSender' => 'Psl/Channel/Internal/UnboundedSender.php',
        'Psl\\Channel\\Internal\\UnboundedReceiver' => 'Psl/Channel/Internal/UnboundedReceiver.php',
        'Psl\\Channel\\Exception\\ClosedChannelException' => 'Psl/Channel/Exception/ClosedChannelException.php',
        'Psl\\Channel\\Exception\\EmptyChannelException' => 'Psl/Channel/Exception/EmptyChannelException.php',
        'Psl\\Channel\\Exception\\FullChannelException' => 'Psl/Channel/Exception/FullChannelException.php',
        'Psl\\Iter\\Exception\\OutOfBoundsException' => 'Psl/Iter/Exception/OutOfBoundsException.php',
        'Psl\\Str\\Exception\\OutOfBoundsException' => 'Psl/Str/Exception/OutOfBoundsException.php',
        'Psl\\Collection\\Exception\\OutOfBoundsException' => 'Psl/Collection/Exception/OutOfBoundsException.php',
        'Psl\\DataStructure\\Exception\\UnderflowException' => 'Psl/DataStructure/Exception/UnderflowException.php',
        'Psl\\Vec\\Exception\\LogicException' => 'Psl/Vec/Exception/LogicException.php',
        'Psl\\File\\Exception\\AlreadyCreatedException' => 'Psl/File/Exception/AlreadyCreatedException.php',
        'Psl\\File\\Exception\\InvalidArgumentException' => 'Psl/File/Exception/InvalidArgumentException.php',
        'Psl\\File\\Exception\\NotFileException' => 'Psl/File/Exception/NotFileException.php',
        'Psl\\File\\Exception\\NotFoundException' => 'Psl/File/Exception/NotFoundException.php',
        'Psl\\File\\Exception\\NotReadableException' => 'Psl/File/Exception/NotReadableException.php',
        'Psl\\File\\Exception\\NotWritableException' => 'Psl/File/Exception/NotWritableException.php',
        'Psl\\Str\\Exception\\InvalidArgumentException' => 'Psl/Str/Exception/InvalidArgumentException.php',
        'Psl\\Str\\Exception\\LogicException' => 'Psl/Str/Exception/LogicException.php',
        'Psl\\Dict\\Exception\\LogicException' => 'Psl/Dict/Exception/LogicException.php',
        'Psl\\Math\\Exception\\OverflowException' => 'Psl/Math/Exception/OverflowException.php',
        'Psl\\Math\\Exception\\InvalidArgumentException' => 'Psl/Math/Exception/InvalidArgumentException.php',
        'Psl\\Iter\\Exception\\InvalidArgumentException' => 'Psl/Iter/Exception/InvalidArgumentException.php',
        'Psl\\PseudoRandom\\Exception\\InvalidArgumentException' => 'Psl/PseudoRandom/Exception/InvalidArgumentException.php',
        'Psl\\Async\\Exception\\InvalidArgumentException' => 'Psl/Async/Exception/InvalidArgumentException.php',
        'Psl\\Option\\Exception\\NoneException' => 'Psl/Option/Exception/NoneException.php',
        'Psl\\Option\\Option' => 'Psl/Option/Option.php',
    ];

    public const ENUMS = [
        'Psl\\File\\LockType' => 'Psl/File/LockType.php',
        'Psl\\File\\WriteMode' => 'Psl/File/WriteMode.php',
        'Psl\\Str\\Encoding' => 'Psl/Str/Encoding.php',
        'Psl\\Network\\SocketScheme' => 'Psl/Network/SocketScheme.php',
        'Psl\\Html\\Encoding' => 'Psl/Html/Encoding.php',
        'Psl\\Hash\\Algorithm' => 'Psl/Hash/Algorithm.php',
        'Psl\\Hash\\Hmac\\Algorithm' => 'Psl/Hash/Hmac/Algorithm.php',
        'Psl\\OS\\OperatingSystemFamily' => 'Psl/OS/OperatingSystemFamily.php',
        'Psl\\Password\\Algorithm' => 'Psl/Password/Algorithm.php',
        'Psl\\Shell\\ErrorOutputBehavior' => 'Psl/Shell/ErrorOutputBehavior.php',
    ];

    public const TYPE_CONSTANTS = 1;

    public const TYPE_FUNCTION = 2;

    public const TYPE_INTERFACE = 4;

    public const TYPE_TRAIT = 8;

    public const TYPE_CLASS = 16;

    public const TYPE_ENUM = 32;

    public const TYPE_CLASSISH = self::TYPE_INTERFACE | self::TYPE_TRAIT | self::TYPE_CLASS | self::TYPE_ENUM;

    private function __construct()
    {
    }

    public static function bootstrap(): void
    {
        self::loadConstants();
        self::autoload(static function (): void {
            self::loadFunctions();
        });
    }

    public static function preload(): void
    {
        self::loadConstants();
        self::autoload(static function (): void {
            self::loadFunctions();
            self::loadInterfaces();
            self::loadTraits();
            self::loadClasses();
            self::loadEnums();
        });
    }

    private static function load(string $file): void
    {
        require_once dirname(__DIR__, 2) . '/' . $file;
    }

    private static function autoload(Closure $callback): void
    {
        $loader = static function (string $classname): ?bool {
            $file = self::lookupClassish($classname);
            if (!$file) {
                return null;
            }

            self::load($file);

            return true;
        };

        spl_autoload_register($loader);
        $callback();
        spl_autoload_unregister($loader);
    }

    private static function loadConstants(): void
    {
        foreach (self::CONSTANTS as $constant => $file) {
            if (defined($constant)) {
                continue;
            }

            self::load($file);
        }
    }

    private static function loadFunctions(): void
    {
        foreach (self::FUNCTIONS as $function => $file) {
            if (function_exists($function)) {
                continue;
            }

            self::load($file);
        }
    }

    private static function loadInterfaces(): void
    {
        foreach (self::INTERFACES as $interface => $file) {
            if (interface_exists($interface)) {
                continue;
            }

            self::load($file);
        }
    }

    private static function loadTraits(): void
    {
        foreach (self::TRAITS as $trait => $file) {
            if (trait_exists($trait)) {
                continue;
            }

            self::load($file);
        }
    }

    private static function loadClasses(): void
    {
        foreach (self::CLASSES as $class => $file) {
            if (class_exists($class)) {
                continue;
            }

            self::load($file);
        }
    }

    private static function loadEnums(): void
    {
        foreach (self::ENUMS as $enum => $file) {
            if (enum_exists($enum)) {
                continue;
            }

            self::load($file);
        }
    }

    private static function lookupClassish(string $classname): ?string
    {
        static $lookup;
        if (!$lookup) {
            $lookup = array_merge(self::TRAITS, self::INTERFACES, self::CLASSES, self::ENUMS);
        }

        return $lookup[$classname] ?? null;
    }
}
