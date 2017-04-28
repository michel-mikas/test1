<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-left image">
              <img src="{{url('/images/uploads/admins/' .Auth::guard('admin')->user()->profile_pic)}}" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
              <p>{{Auth::guard('admin')->user()->name}}</p>
            </div>
          </div>
        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            <!-- Optionally, you can add icons to the links -->
            @foreach($page_params['side_bar'] as $linkName => $link)
                <li class="treeview @if($link['is_active']) active @endif">
                    <a href="{{$link['href']}}">
                    <i class="{{$link['fa_icon']}}"></i>
                    <span>{{$linkName}}</span>
                    @if(isset($link['label']))
                        <span class="label pull-right {{$link['label']['class']}}">{!!$link['label']['data']!!}</span>
                    @endif
                    @if(isset($link['dropdown']))
                        <i class="fa fa-angle-left pull-right @if(isset($link['label'])) hidden @endif"></i>
                        </a>
                        <ul class="treeview-menu">
                            @foreach($link['dropdown'] as $subLinkName => $subLink)
                                <li class="@if($subLink['is_active']) active @endif">
                                    <a href="{{$subLink['href']}}"><i class="{{$subLink['fa_icon']}}"></i>&nbsp;{{$subLinkName}}
                                        @if(isset($subLink['label']))
                                            <span class="label pull-right {{$subLink['label']['class']}}">{!!$subLink['label']['data']!!}</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        </a>
                    @endif
                </li>
            @endforeach
        </ul><!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>