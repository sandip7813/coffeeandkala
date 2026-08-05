<aside class="editorial-sidebar" id="editorialSidebar">
    <ul class="sidebar-nav-list">
        @if ($slot->isNotEmpty())
            {{ $slot }}
        @else
            <li>
                <a href="javascript:void(0)" class="nav-item active" data-target="sec-01">
                    <span class="nav-item-number">01</span>
                    <div class="nav-item-title">Stories worth Sitting Down For</div>
                    <div class="nav-item-desc">Art. Culture. Travel. People. Ideas.</div>
                    <i class="fa-solid fa-arrow-down nav-item-arrow"></i>
                </a>
            </li>
            <li>
                <a href="javascript:void(0)" class="nav-item" data-target="sec-02">
                    <span class="nav-item-number">02</span>
                    <div class="nav-item-title">Thought of the day</div>
                    <div class="nav-item-desc">A quiet line for a slower morning.</div>
                    <i class="fa-solid fa-arrow-down nav-item-arrow"></i>
                </a>
            </li>
            <li>
                <a href="javascript:void(0)" class="nav-item" data-target="sec-03">
                    <span class="nav-item-number">03</span>
                    <div class="nav-item-title">Currently Pouring</div>
                    <div class="nav-item-desc">Featured stories, poured slowly.</div>
                    <i class="fa-solid fa-arrow-down nav-item-arrow"></i>
                </a>
            </li>
            <li>
                <a href="javascript:void(0)" class="nav-item" data-target="sec-04">
                    <span class="nav-item-number">04</span>
                    <div class="nav-item-title">Three Small Stories</div>
                    <div class="nav-item-desc">Brief pours for a quieter pause.</div>
                    <i class="fa-solid fa-arrow-down nav-item-arrow"></i>
                </a>
            </li>
            <li>
                <a href="javascript:void(0)" class="nav-item" data-target="sec-05">
                    <span class="nav-item-number">05</span>
                    <div class="nav-item-title">Features Unfolded</div>
                    <div class="nav-item-desc">Longer reads that ask you to stay.</div>
                    <i class="fa-solid fa-arrow-down nav-item-arrow"></i>
                </a>
            </li>
            <li>
                <a href="javascript:void(0)" class="nav-item" data-target="sec-06">
                    <span class="nav-item-number">06</span>
                    <div class="nav-item-title">Gallery of Visual Storytelling</div>
                    <div class="nav-item-desc">Frames that refuse to stay quiet.</div>
                    <i class="fa-solid fa-arrow-down nav-item-arrow"></i>
                </a>
            </li>
            <li>
                <a href="javascript:void(0)" class="nav-item" data-target="sec-07">
                    <span class="nav-item-number">07</span>
                    <div class="nav-item-title">From the Journal</div>
                    <div class="nav-item-desc">Notes, essays, and quiet reads.</div>
                    <i class="fa-solid fa-arrow-down nav-item-arrow"></i>
                </a>
            </li>
            <li>
                <a href="javascript:void(0)" class="nav-item" data-target="sec-08">
                    <span class="nav-item-number">08</span>
                    <div class="nav-item-title">Gallery of visual poetry</div>
                    <div class="nav-item-desc">Where colour finds its own language</div>
                    <i class="fa-solid fa-arrow-down nav-item-arrow"></i>
                </a>
            </li>
        @endif
    </ul>
</aside>
