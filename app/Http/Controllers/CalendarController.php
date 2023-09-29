<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function showdata(Request $request)
    {
        if ($request->ajax()) {
            $data = Event::leftjoin('users', 'users.id', 'events.userid')
                ->leftjoin('department', 'department.dept_id', 'users.dept_id')
                ->whereDate('events.start', '>=', $request->start)
                ->whereDate('events.end', '<=', $request->end)
                ->where('events.calendar', '=', 1)
                ->where('events.status', '=', 1)
                ->select('events.*', 'users.name', 'users.surname', 'department.dept_name')->get();

            $result = array();
            foreach ($data as $list) {
                $result[] = array(
                    "title" => $list->title,
                    "description" => $list->description,
                    "start" => Carbon::parse($list->start)->format('Y-m-d'),
                    "end" => Carbon::parse($list->end)->addDays()->format('Y-m-d'),
                    "name" => $list->name,
                    "surname" => $list->surname,
                    "dept_name" => $list->dept_name,
                    "updated_at" => $list->updated_at,
                    "color" => $list->color,
                    "allDay" => true,
                );
            }
            return response()->json($result);
        }
    }
}