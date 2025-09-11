<div {!! $htmlAttributes !!}>
    <div class="col mt-4 mt-md-0 shop-toolbar-item-view-changer">
        <div class="d-flex justify-content-md-end justify-content-center shop-view">
            <a class="grid-view {{$productView() === 'list' ? 'active' : ''}}"
               data-toggle="tooltip" data-placement="top" title="Grid view"
               href="{{request()->fullUrlWithQuery(['currentPage' => $currentPage,'view'=> 'grid' ])}}">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="current"
                     xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M6.42857 0.000167847H1.28571C0.575634 0.000167847 0 0.575802 0 1.28588V6.42874C0 7.13882 0.575634 7.71445 1.28571 7.71445H6.42857C7.13865 7.71445 7.71429 7.13882 7.71429 6.42874V1.28588C7.71429 0.575802 7.13865 0.000167847 6.42857 0.000167847Z"
                        fill="#current"/>
                    <path
                        d="M16.7137 0.000167847H11.5709C10.8608 0.000167847 10.2852 0.575802 10.2852 1.28588V6.42874C10.2852 7.13882 10.8608 7.71445 11.5709 7.71445H16.7137C17.4238 7.71445 17.9994 7.13882 17.9994 6.42874V1.28588C17.9994 0.575802 17.4238 0.000167847 16.7137 0.000167847Z"
                        fill="#current"/>
                    <path
                        d="M6.42857 10.2857H1.28571C0.575634 10.2857 0 10.8614 0 11.5714V16.7143C0 17.4244 0.575634 18 1.28571 18H6.42857C7.13865 18 7.71429 17.4244 7.71429 16.7143V11.5714C7.71429 10.8614 7.13865 10.2857 6.42857 10.2857Z"
                        fill="#current"/>
                    <path
                        d="M16.7137 10.2857H11.5709C10.8608 10.2857 10.2852 10.8614 10.2852 11.5714V16.7143C10.2852 17.4244 10.8608 18 11.5709 18H16.7137C17.4238 18 17.9994 17.4244 17.9994 16.7143V11.5714C17.9994 10.8614 17.4238 10.2857 16.7137 10.2857Z"
                        fill="#current"/>
                </svg>
            </a>
            <a class="list-view {{$productView() === 'grid' ? 'active' : ''}}"
               data-toggle="tooltip" data-placement="top" title="List view"
               href="{{request()->fullUrlWithQuery(['currentPage' => $currentPage,'view'=> 'list' ])}}">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="current"
                     xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M3.33333 0H0.666667C0.298477 0 0 0.298477 0 0.666667V3.33333C0 3.70152 0.298477 4 0.666667 4H3.33333C3.70152 4 4 3.70152 4 3.33333V0.666667C4 0.298477 3.70152 0 3.33333 0Z"
                        fill="#current"/>
                    <path
                        d="M3.33333 7H0.666667C0.298477 7 0 7.29848 0 7.66667V10.3333C0 10.7015 0.298477 11 0.666667 11H3.33333C3.70152 11 4 10.7015 4 10.3333V7.66667C4 7.29848 3.70152 7 3.33333 7Z"
                        fill="#current"/>
                    <path
                        d="M3.33333 14H0.666667C0.298477 14 0 14.2985 0 14.6667V17.3333C0 17.7015 0.298477 18 0.666667 18H3.33333C3.70152 18 4 17.7015 4 17.3333V14.6667C4 14.2985 3.70152 14 3.33333 14Z"
                        fill="#current"/>
                    <rect x="6" width="12" height="4" rx="1" fill="#current"/>
                    <rect x="6" y="7" width="12" height="4" rx="1" fill="#current"/>
                    <rect x="6" y="14" width="12" height="4" rx="1" fill="#current"/>
                </svg>
            </a>
        </div>
    </div>
</div>>
