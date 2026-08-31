<aside class="editorial-sidebar" id="editorialSidebar">
    <ul class="sidebar-nav-list">
        @if ($slot->isNotEmpty())
            {{ $slot }}
        @else
            <li>
                <a href="javascript:void(0)" class="nav-item active" data-target="sec-01">
                    <span class="nav-item-number">01</span>
                    <div class="nav-item-title">The Home Of Slow Publishing</div>
                    <i class="fa-solid fa-arrow-down nav-item-arrow"></i>
                </a>
            </li>
            <li>
                <a href="javascript:void(0)" class="nav-item" data-target="sec-02">
                    <span class="nav-item-number">02</span>
                    <div class="nav-item-title">Thought of the day</div>
                    <i class="fa-solid fa-arrow-down nav-item-arrow"></i>
                </a>
            </li>
            <li>
                <a href="javascript:void(0)" class="nav-item" data-target="sec-03">
                    <span class="nav-item-number">03</span>
                    <div class="nav-item-title">The Edit</div>
                    <div class="nav-item-desc">Latest editorial selection</div>
                    <i class="fa-solid fa-arrow-down nav-item-arrow"></i>
                </a>
            </li>
            <li>
                <a href="javascript:void(0)" class="nav-item" data-target="sec-04">
                    <span class="nav-item-number">04</span>
                    <div class="nav-item-title">Short Reads</div>
                    <div class="nav-item-desc">Brief stories, ideas & observations</div>
                    <i class="fa-solid fa-arrow-down nav-item-arrow"></i>
                </a>
            </li>
            <li>
                <a href="javascript:void(0)" class="nav-item" data-target="sec-05">
                    <span class="nav-item-number">05</span>
                    <div class="nav-item-title">Features</div>
                    <div class="nav-item-desc">A closer look</div>
                    <i class="fa-solid fa-arrow-down nav-item-arrow"></i>
                </a>
            </li>
            <li>
                <a href="javascript:void(0)" class="nav-item" data-target="sec-06">
                    <span class="nav-item-number">06</span>
                    <div class="nav-item-title">Gallery</div>
                    <div class="nav-item-desc">A collection of photography & visual expression</div>
                    <i class="fa-solid fa-arrow-down nav-item-arrow"></i>
                </a>
            </li>
            <li>
                <a href="javascript:void(0)" class="nav-item" data-target="sec-07">
                    <span class="nav-item-number">07</span>
                    <div class="nav-item-title">The Journal</div>
                    <div class="nav-item-desc">Perspectives on everything between</div>
                    <i class="fa-solid fa-arrow-down nav-item-arrow"></i>
                </a>
            </li>
            <li>
                <a href="javascript:void(0)" class="nav-item" data-target="sec-08">
                    <span class="nav-item-number">08</span>
                    <div class="nav-item-title">Studio</div>
                    <div class="nav-item-desc">A collection of art & creative expression</div>
                    <i class="fa-solid fa-arrow-down nav-item-arrow"></i>
                </a>
            </li>
            <li>
                <a href="javascript:void(0)" class="nav-item" data-target="sec-09">
                    <span class="nav-item-number">09</span>
                    <div class="nav-item-title">Poetry</div>
                    <div class="nav-item-desc">Words, Verses & Stories</div>
                    <i class="fa-solid fa-arrow-down nav-item-arrow"></i>
                </a>
            </li>
        @endif
    </ul>
</aside>
