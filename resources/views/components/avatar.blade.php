<div
@class([
	'avatar',
	$avatar_size,
])
@style([
	'background-image: url(' . $image . ')' => !empty($image),
])>
	@empty($image)
		{{ $initials }}
	@endempty
</div>
