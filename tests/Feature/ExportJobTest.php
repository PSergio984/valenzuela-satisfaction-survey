<?php

use App\Exports\SurveyResponsesExport;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

test('export survey responses is queued', function () {
    Excel::fake();
    Carbon::setTestNow(now());

    $user = User::factory()->create();
    $survey = Survey::factory()->create();
    $filename = "exports/survey-{$survey->slug}-responses-".now()->format('Y-m-d-His').'.xlsx';

    $this->actingAs($user)
        ->get(route('admin.surveys.export.excel', $survey));

    Excel::assertQueued($filename, 'private');
});

test('export job saves file to private disk', function () {
    Excel::fake();
    Storage::fake('private');
    $user = User::factory()->create();
    $survey = Survey::factory()->create();
    $filename = "exports/survey-{$survey->id}.xlsx";

    $export = new SurveyResponsesExport($survey->id, $user->id, $filename);

    Excel::store($export, $filename, 'private');

    Excel::assertStored($filename, 'private');
});
