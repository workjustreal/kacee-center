<!-- start status form -->
@php
    // รออนุมัติ
    $pendingActive = '';
    // หัวหน้าอนุมัติ
    $approved1Active = '';
    // ดำเนินการ
    $approved2Active = '';
    $progressActive = '';
    // รอตรวจสอบ
    $approved2checkActive = '';
    // ผ่านการตรวจสอบ
    $user_approvedActive = '';
    // เสร็จสิ้น
    $completedActive = '';
    // ยกเลิกโดยผู้แจ้ง
    $cancel1Active = '';
    // ยกเลิกโดยผู้อนุมัติ
    $cancel2Active = '';
    // ยกเลิกโดยผู้รับงาน
    $cancel3Active = '';
    
    if ($repairs->status == 'รออนุมัติ') {
        $pendingActive = 'active';
    } elseif ($repairs->status == 'หัวหน้าอนุมัติ') {
        $approved1Active = 'active';
    } elseif ($repairs->status == 'ดำเนินการ') {
        $progressActive = 'active';
    } elseif ($repairs->status == 'รอตรวจสอบ') {
        $approved2checkActive = 'active';
    } elseif ($repairs->status == 'ผ่านการตรวจสอบ') {
        $user_approvedActive = 'active';
    } elseif ($repairs->status == 'เสร็จสิ้น') {
        $completedActive = 'active';
    } elseif ($repairs->status == 'ยกเลิกโดยผู้แจ้ง') {
        $cancel1Active = 'active';
    } elseif ($repairs->status == 'ยกเลิกโดยหัวหน้า') {
        $cancel2Active = 'active';
    } elseif ($repairs->status == 'ยกเลิกโดยผู้รับงาน') {
        $cancel3Active = 'active';
    }
    // echo $repairs->status;
@endphp

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="time-line-box">
                    <div class="swiper-container text-center">
                        <div class="swiper-wrapper">
                            {{-- รออนุมัติ --}}
                            <div class="swiper-slide">
                                <div class="timestamp">
                                    <span class="date">
                                        {{ $thaiDateHelper->shortDateFormat($repairs->created_at) }},
                                        <small
                                            class="text-muted">{{ \Carbon\Carbon::parse($repairs->created_at)->format('H:i') . ' น.' }}</small>
                                    </span>
                                </div>
                                <div class="status">
                                    <span>ผู้แจ้ง</span>
                                </div>
                            </div>
                            
                            {{-- ผู้อนุมัติ --}}
                            <div class="swiper-slide">
                                <div class="timestamp">
                                    <span class="date">
                                        @if ($repairs->approve_date != '')
                                            {{ $thaiDateHelper->shortDateFormat($repairs->approve_date) }}
                                            <small
                                                class="text-muted">{{ \Carbon\Carbon::parse($repairs->approve_date)->format('H:i') . ' น.' }}</small>
                                        @else
                                            &nbsp;
                                        @endif
                                    </span>
                                </div>
                                <div class="status {{ $pendingActive }}">
                                    <span>ผู้อนุมัติ</span>
                                </div>
                            </div>

                            {{-- ผู้รับแจ้ง --}}
                            <div class="swiper-slide">
                                <div class="timestamp">
                                    <span class="date">
                                        @if ($repairs->manager_date != '')
                                            {{ $thaiDateHelper->shortDateFormat($repairs->manager_date) }}
                                            <small
                                                class="text-muted">{{ \Carbon\Carbon::parse($repairs->manager_date)->format('H:i') . ' น.' }}</small>
                                        @else
                                            &nbsp;
                                        @endif
                                    </span>
                                </div>
                                <div class="status {{ $approved1Active }}">
                                    <span>ผู้รับแจ้ง</span>
                                </div>
                            </div>

                            {{-- กำลังดำเนินการ --}}
                            @if ($tech_detail)
                                @foreach ($tech_detail as $dt)
                                    <div class="swiper-slide">
                                        <div class="timestamp">
                                            <span class="date">
                                                {{ $thaiDateHelper->shortDateFormat($dt['start_date']) }}
                                                <small
                                                    class="text-muted">{{ \Carbon\Carbon::parse($dt['start_date'])->format('H:i') . ' น.' }}</small>
                                            </span>
                                        </div>
                                        <div class="status {{ $progressActive }}">
                                            <span>กำลังดำเนินการ</span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="swiper-slide">
                                    <div class="timestamp">
                                        <span class="date">
                                            &nbsp;
                                        </span>
                                    </div>
                                    <div class="status {{ $progressActive }} ">
                                        <span>กำลังดำเนินการ</span>
                                    </div>
                                </div>
                            @endif

                            {{-- ผู้ตรวจสอบ --}}
                            @if ($ap_detail)
                                @foreach ($ap_detail as $ap)
                                    <div class="swiper-slide">
                                        <div class="timestamp">
                                            <span class="date">
                                                {{ $thaiDateHelper->shortDateFormat($ap['date']) }}
                                                <small
                                                    class="text-muted">{{ \Carbon\Carbon::parse($ap['date'])->format('H:i') . ' น.' }}</small>
                                            </span>
                                        </div>
                                        <div class="status {{ $approved2checkActive }} ">
                                            <span>ผู้ตรวจสอบ</span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="swiper-slide">
                                    <div class="timestamp">
                                        <span class="date">&nbsp;</span>
                                    </div>
                                    <div class="status {{ $approved2checkActive }}">
                                        <span>ผู้ตรวจสอบ</span>
                                    </div>
                                </div>
                            @endif

                            {{-- ผู้ตรวจรับงาน --}}
                            @if ($user_detail)
                                @foreach ($user_detail as $ud)
                                    <div class="swiper-slide">
                                        <div class="timestamp">
                                            <span class="date">
                                                {{ $thaiDateHelper->shortDateFormat($ud['date']) }}
                                                <small
                                                    class="text-muted">{{ \Carbon\Carbon::parse($ud['date'])->format('H:i') . ' น.' }}</small>
                                            </span>
                                        </div>
                                        <div class="status {{ $user_approvedActive }}">
                                            <span>ผู้ตรวจรับงาน</span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="swiper-slide">
                                    <div class="timestamp">
                                        <span class="date">&nbsp;</span>
                                    </div>
                                    <div class="status {{ $user_approvedActive }}">
                                        <span>ผู้ตรวจรับงาน</span>
                                    </div>
                                </div>
                            @endif

                            {{-- เสร็จสิ้น --}}
                            <div class="swiper-slide">
                                @if ($repairs->status == 'เสร็จสิ้น')
                                    <div class="timestamp">
                                        <span class="date">
                                            {{ $thaiDateHelper->shortDateFormat($repairs->updated_at) }}
                                            <small
                                                class="text-muted">{{ \Carbon\Carbon::parse($repairs->updated_at)->format('H:i') . ' น.' }}</small>
                                        </span>
                                    </div>
                                @else
                                    <div class="timestamp">
                                        <span class="date">&nbsp;</span>
                                    </div>
                                @endif
                                <div class="status {{ $completedActive }}">
                                    <span>เสร็จสิ้น</span>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-pagination mt-1"></div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end status form -->
