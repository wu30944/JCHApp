<!-- Header Carousel -->
    <script>
    $('.carousel').carousel({
        interval: 5000 //changes the speed
    })
    </script>
<header id="myCarousel" class="carousel slide">
<div class="content full">
	<!-- Indicators -->
	@if(isset($dtCarousel) && count($dtCarousel)>0)
		<ol class="carousel-indicators">
			@for($i=0;$i<count($dtCarousel);$i++)
				@if($i==0)
					<li data-target="#myCarousel" data-slide-to="{{$i}}" class="active"></li>
				@else
					<li data-target="#myCarousel" data-slide-to="{{$i}}"></li>
				@endif
			@endfor
		</ol>
	@endif



	<!-- Wrapper for slides -->
	@if(isset($dtCarousel) && count($dtCarousel)>0)
		<div class="carousel-inner">
			@for($i=0;$i<count($dtCarousel);$i++)
				@if($i==0)
					<div class="item active">
						<div class="fill">
							<img class="img-responsive"  alt="" id="preview" src="{{$dtCarousel[$i]->photo_path}}" style="width: 2156px; height: 280px;">
						</div>
						<div class="carousel-caption">
							{{-- <h2>Caption 1 Test</h2> --}}
						</div>
					</div>
				@else
					<div class="item">
						<div class="fill">
							<img class="img-responsive"  alt="" id="preview" src="{{$dtCarousel[$i]->photo_path}}"  style="width: 2156px; height: 280px;">
						</div>
						<div class="carousel-caption">
							{{-- <h2>Caption 2</h2> --}}
						</div>
					</div>
				@endif
			@endfor
		</div>
	@endif

	<!-- Controls -->
	<a class="left carousel-control" href="#myCarousel" data-slide="prev">
		<span class="icon-prev"></span>
	</a> 
	<a class="right carousel-control" href="#myCarousel"
		data-slide="next"> <span class="icon-next"></span>
	</a>
	</div>
</header>