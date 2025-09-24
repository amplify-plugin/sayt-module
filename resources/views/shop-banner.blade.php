<div {!! $htmlAttributes !!}>
    <div class="owl-carousel"
         data-owl-carousel='{"nav":true,"dots":false,"loop":true}'>
        @foreach($banners as $banner)
            <a href="{{ $banner->getUrl() }}" class="w-100">
                @switch($banner->getTriggerType())
                    @case(5)
                        <img src="{{ $banner->getImage() }}"
                             id="{{ $banner->getId() }}"
                             alt="{{ $banner->getAlt() }}"
                             style="height: 250px; width: 100%; object-fit: contain"/>
                        @break

                    @case(3)
                        <div style="height: 250px; width: 100%; display: block">
                            {{ $banner->getHtml() }}
                        </div>
                        @break
                @endswitch
            </a>
        @endforeach
    </div>
    @if($showCloseButton)
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    @endif
</div>
