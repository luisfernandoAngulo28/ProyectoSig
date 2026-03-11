@foreach($items as $item)

    @if(!$mobile)
        <li @if($item->hasChildren()||$item->url()==url('Libreria')) class="dropdown_hasmenu" @endif @if($item->title=='Ayuda') style="display:none" @endif>
        {!! Func::menu_link($item, 1) !!}
            @if($item->hasChildren())
                <ul class="dropdown">
                    @foreach($item->children() as $child)
                        @if($child->hasChildren())
                            <li>
                        @else
                            <li>
                        @endif
                            {!! Func::menu_link($child, 2) !!}
                            @if($child->hasChildren())
                                <ul class="dropdown">
                                    @foreach($child->children() as $child2)
                                        <li>{!! Func::menu_link($child2, 3) !!}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @elseif($item->url()==url('Libreria'))
                <ul class="dropdown">
                    <li>
                        <a href="{{ url('todos') }}">Todos</a>
                    </li>
                    {{-- @if(count(\Solunes\Business\App\ProductBridge::where('discount_price', '>', 0)->first())>0) --}}
                    <li>
                        <a href="{{ url('ofertas') }}">Ofertas</a>
                    </li>
                    {{-- @endif --}}
                    @foreach(\Solunes\Business\App\Category::whereNull('parent_id')->get() as $category)
                        @if(count($category->products) >= 1)
                            <li>
                                <a href="{{ url('categoria/'.$category->id) }}">{{ $category->name }}</a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @endif

            @if ($item->url()==url('Centro-de-documentación'))
            <ul class="dropdown">
                <li>
                    <a href="{{ url('general') }}">General</a>
                </li>
              
             
            </ul>
            @endif
        </li>
    @else 
        <li @if($item->hasChildren()||$item->url()==url('Libreria')) class="menu-item-has-children" @endif @if($item->title=='Ayuda') style="display:none" @endif>
            @if ($item->hasChildren()||$item->url()==url('Libreria'))
                <a href="#">{{ $item->title }}</a>
            @else
                <a href="{{ $item->url() }}">{{ $item->title }}</a>
            @endif
            @if($item->hasChildren())
                <ul class="dropdown">
                    <li>
                        <a href="{{ $item->url() }}">{{ $item->title }}</a>
                    </li>
                    @foreach($item->children() as $child)
                        @if($child->hasChildren())
                            <li>
                        @else
                            <li>
                        @endif
                            {{-- {!! Func::menu_link($child, 2) !!} --}}
                            <a href="{{ $child->url() }}">{{ $child->title }}</a>
                            @if($child->hasChildren())
                                <ul class="dropdown">
                                    @foreach($child->children() as $child2)
                                        <li>{!! Func::menu_link($child2, 3) !!}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @elseif($item->url()==url('Libreria'))
                <ul class="dropdown">
                    <li>
                        <a href="{{ url('jugueteria') }}">Todos</a>
                    </li>
                    <li>
                        <a href="{{ url('ofertas') }}">Ofertas</a>
                    </li>
                    {{-- @if(count(\Solunes\Business\App\ProductBridge::orWhereIn('id', $products)->orWhereIn('category_id', $categories)->where('quantity', '>', 0 )->where('active', 1)->orWhere('discount_price', '>', 0)->first())>0) --}}
                    
                    {{-- @endif --}}
                    <li>
                        <a href="{{ $item->url() }}">{{ $item->title }}</a>
                    </li>

                    @foreach(\Solunes\Business\App\Category::whereNull('parent_id')->get() as $category)
                        <li>
                            <a href="{{ url('categoria/'.$category->id) }}">{{ $category->name }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
    @endif

    @if($item->divider)
        <li{{\HTML::attributes($item->divider)}}></li>
    @endif
@endforeach
