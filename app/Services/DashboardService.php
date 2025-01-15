<?php

namespace App\Services;

use App\Interfaces\RepsitotiesInterfaces\DashboardRepositoryInterface;
use App\Interfaces\ServicesInterfaces\DashboardServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\PhpWord;

class DashboardService implements DashboardServiceInterface
{
    private DashboardRepositoryInterface $dashboardRepository;

    public function __construct(DashboardRepositoryInterface $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    public function getDashboardData(Request $request): array
    {
        $respData = $this->dashboardRepository->getDashboardData();
        $groupsStat = [];
        $today = date('Y-m-d');
        if (isset($respData['groups'])) {
            $respData['groups']->each(function ($item, $index) use (&$groupsStat, $today) {
                $groupsStat[$index]['group_id'] = $item->id;
                $groupsStat[$index]['group_title'] = $item->title;
                if (!empty($item->teachers)) {
                    $educational_events = collect();
                    $item->teachers->each(function ($teacher, $indexTeacher) use (&$groupsStat, &$educational_events, $index) {
                        $educational_events = $educational_events->merge($teacher->educational_events);
                        $groupsStat[$index]['date_start'] = isset($groupsStat[$index]['date_start']) ? min($groupsStat[$index]['date_start'], $teacher->pivot->date_start) : $teacher->pivot->date_start;
                        $groupsStat[$index]['date_finish'] = isset($groupsStat[$index]['date_finish']) ? min($groupsStat[$index]['date_finish'], $teacher->pivot->date_finish) : $teacher->pivot->date_finish;
                        $groupsStat[$index]['teachers'][$indexTeacher] = $teacher->user->last_name . " " . $teacher->user->first_name . " " . $teacher->user->patronymic_name;
                    });

                    $groupsStat[$index] = array_merge($groupsStat[$index], $this->getEducEvStat($educational_events, $today));
                }
                $groupsStat[$index]['children_count'] = $item->children_count;
            });
            $respData['groups'] = $groupsStat;

        }
        if (isset($respData['teachers'])) {
            $teachersStat = [];
            $respData['teachers']->each(function ($item, $index) use (&$teachersStat, $today) {
                $teachersStat[$index]['teacher_id'] = $item->id;
                $teachersStat[$index]['teacher_full_name'] = $item->user->last_name . " " . $item->user->first_name . " " . $item->user->patronymic_name;
                $teachersStat[$index]['group'] = $item->groups[0]->title;
                $teachersStat[$index] = array_merge($teachersStat[$index], $this->getEducEvStat($item->educational_events, $today));
            });
            $respData['teachers'] = $teachersStat;
        }
        if (isset($respData['childrens'])) {
            $childrenStat = [];
            $respData['childrens']->each(function ($item, $index) use (&$childrenStat, $today) {
                $childrenStat[$index]['child_id'] = $item->id;
                $childrenStat[$index]['child_full_name'] = $item->user->last_name . " " . $item->user->first_name . " " . $item->user->patronymic_name;
                $childrenStat[$index]['group'] = $item->group->title;
                $childrenStat[$index]['birth_year'] = $item->user->birth_year;
                $childrenStat[$index]['age'] =  Carbon::parse($item->user->birth_date)->age;
                $childrenStat[$index]['visited_educational_events'] = $item->visited_educational_events->count();
                $childrenStat[$index]['avg_estimation_mark'] = $item->visited_educational_events->flatMap(function ($event) {
                    return $event->children_visitors->pluck('pivot.estimation_mark')->filter();
                })->avg();
                $childrenStat[$index]['avg_estimation_mark'] = round($childrenStat[$index]['avg_estimation_mark'], 2);
            });
            $respData['childrens'] = $childrenStat;
        }

        return $respData;
    }

    private function getEducEvStat($educational_events, $today)
    {
        $past_events = $educational_events->filter(function ($event) use ($today) {
            return $event->event_date < $today;
        });
        $past_events_count = $past_events->count();
        $total_events_count = $educational_events->count();
        $past_events_percentage = ($total_events_count > 0)
            ? ($past_events_count / $total_events_count) * 100
            : 0;
        $average_estimation_mark = $past_events->flatMap(function ($event) {
            return $event->children_visitors->pluck('pivot.estimation_mark')->filter();
        })->avg();
        return ['average_estimation_mark' => round($average_estimation_mark, 2),
            'past_events_percentage' => round($past_events_percentage, 2)];
    }

    public function getDashboardGroupReportWord(array $data): string
    {
        $reportData = $this->dashboardRepository->getDashboardGroupReportData($data);

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $section->addText("Звіт \"Список групи\"", array('bold' => true, 'size' => 16, 'name' => 'Times New Roman'));
        $textRun = $section->addTextRun();
        $textRun->addText("Назва групи – ", array('bold' => true, 'size' => 16, 'name' => 'Times New Roman'));
        $textRun->addText( $reportData->title, array('bold' => false, 'size' => 16, 'name' => 'Times New Roman'));
        $textRun = $section->addTextRun();
        $textRun->addText('Дата оформлення звіту: ', array('bold' => true, 'size' => 14, 'name' => 'Times New Roman'));
        $textRun->addText(date('Y-m-d'), array('bold' => false, 'size' => 14, 'name' => 'Times New Roman'));
        $textRun = $section->addTextRun();
        $textRun->addText('Підзвітний період: ', array('bold' => true, 'size' => 14, 'name' => 'Times New Roman'));
        $textRun->addText($data['from'] . ' – ' . $data['to'], array('bold' => false, 'size' => 14, 'name' => 'Times New Roman'));
        $section->addText('Вихователі групи: ', array('bold' => true, 'size' => 14, 'name' => 'Times New Roman'));
        if (!empty($reportData->teachers)) {
            $reportData->teachers->each(function ($teacher) use ($section) {
                $teacherText = $teacher->user->last_name . " " . $teacher->user->first_name . " " . $teacher->user->patronymic_name;
                $teacherText .= " - (контактний телефон: " . $teacher->phone . ")";
                $section->addListItem($teacherText, 0, array('bold' => false, 'size' => 14, 'name' => 'Times New Roman'));
            });
        }
        $section->addText('Діти: ', array('bold' => true, 'size' => 14, 'name' => 'Times New Roman'));
        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
        $table->addRow();
        $table->addCell(1000)->addText('№', array('bold' => true, 'size' => 12, 'name' => 'Times New Roman'));
        $table->addCell()->addText('ПІБ дитини', array('bold' => true, 'size' => 12, 'name' => 'Times New Roman'));
        $table->addCell()->addText('вік дитини', array('bold' => true, 'size' => 12, 'name' => 'Times New Roman'));
        $table->addCell()->addText('Батьки', array('bold' => true, 'size' => 12, 'name' => 'Times New Roman'));
        if (!empty($reportData->children)) {
            $reportData->children->each(function ($child, $index) use ($table) {
                $table->addRow();
                $table->addCell()->addText($index + 1, array('bold' => false, 'size' => 12, 'name' => 'Times New Roman'));
                $childText = $child->user->last_name . " " . $child->user->first_name . " " . $child->user->patronymic_name;
                $table->addCell()->addText($childText, array('bold' => false, 'size' => 12, 'name' => 'Times New Roman'));
                $table->addCell()->addText(Carbon::parse($child->user->birth_date)->age, array('bold' => false, 'size' => 12, 'name' => 'Times New Roman'));

                $cell = $table->addCell();
                if (!empty($child->parrent_relations)) {
                    $child->parrent_relations->each(function ($parent) use (&$cell) {
                        $parentsText = '';
                        $parentsText .= $parent->user->last_name . " " . $parent->user->first_name . " " . $parent->user->patronymic_name . " ";
                        $parentsText .=  "(" . $parent->pivot->relations . ") - Контактний телефон:  " . $parent->phone;
                        $cell->addText($parentsText, array('bold' => false, 'size' => 12, 'name' => 'Times New Roman'));
                    });
                }
            });
        }
        $fileName = "reports/wordreports/". date('Y_m_d_H_i_s') . "_groups_report.docx";
        $filePath = storage_path("app/public/{$fileName}");
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        $phpWord->save($filePath, 'Word2007');
        return $filePath;
    }

    public function getDashboardGroupReportExcel(array $data): string
    {
        $reportData = $this->dashboardRepository->getDashboardGroupReportData($data);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', "Звіт \"Список групи\"");
        $sheet->setCellValue('B3', "Назва групи – " . $reportData->title);
        $sheet->setCellValue('D3', "Дата оформлення звіту: " . date('Y-m-d'));
        $sheet->setCellValue('D5', "Підзвітний період: " . $data['from'] . ' – ' . $data['to']);
        $sheet->setCellValue('B7', "Вихователі групи: " );
        $rowCounter = 9;
        if (!empty($reportData->teachers)) {
            $reportData->teachers->each(function ($teacher) use ($sheet, &$rowCounter) {
                $teacherText = $teacher->user->last_name . " " . $teacher->user->first_name . " " . $teacher->user->patronymic_name;
                $teacherText .= " - (контактний телефон: " . $teacher->phone . ")";
                $sheet->setCellValue( "D{$rowCounter}", $teacherText);
                $rowCounter++;
            });
            $rowCounter++;
        }
        $sheet->setCellValue( "B{$rowCounter}", 'Діти: ');
        $rowCounter++;
        $sheet->setCellValue( "A{$rowCounter}", '№');
        $sheet->setCellValue( "B{$rowCounter}", 'ПІБ дитини');
        $sheet->setCellValue( "C{$rowCounter}", 'вік дитини');
        $sheet->setCellValue( "D{$rowCounter}", 'Батьки');
        $rowCounter++;
        if (!empty($reportData->children)) {
            $reportData->children->each(function ($child, $index) use ($sheet, &$rowCounter) {
                $sheet->setCellValue( "A{$rowCounter}",$index + 1);
                $childText = $child->user->last_name . " " . $child->user->first_name . " " . $child->user->patronymic_name;
                $sheet->setCellValue( "B{$rowCounter}",$childText);
                $sheet->setCellValue( "C{$rowCounter}", Carbon::parse($child->user->birth_date)->age);

                if (!empty($child->parrent_relations)) {
                    $parentsText = '';
                    $child->parrent_relations->each(function ($parent) use (&$parentsText) {
                        $parentsText .= $parent->user->last_name . " " . $parent->user->first_name . " " . $parent->user->patronymic_name . " ";
                        $parentsText .=  "(" . $parent->pivot->relations . ") - Контактний телефон:  " . $parent->phone . "\n";
                    });
                    $sheet->setCellValue("D{$rowCounter}", $parentsText);
                    $sheet->getStyle("D{$rowCounter}")->getAlignment()->setWrapText(true);
                }
                $rowCounter++;
            });
        }
        $fileName = "reports/xlsreports/". date('Y_m_d_H_i_s') . "_groups_report.xlsx";
        $filePath = storage_path("app/public/{$fileName}");
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        return $filePath;
    }

    public function getDashboardEducationalEventsReportWord(array $data): string
    {
        $reportData = $this->dashboardRepository->getDashboardEducationalEventsReportData($data);
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $section->addText("Звіт Про проведені навчальні заходи", array('bold' => true, 'size' => 16, 'name' => 'Times New Roman'));
        $textRun = $section->addTextRun();
        $textRun->addText('Дата оформлення звіту: ', array('bold' => true, 'size' => 14, 'name' => 'Times New Roman'));
        $textRun->addText(date('Y-m-d'), array('bold' => false, 'size' => 14, 'name' => 'Times New Roman'));
        $textRun = $section->addTextRun();
        $textRun->addText('Підзвітний період: ', array('bold' => true, 'size' => 14, 'name' => 'Times New Roman'));
        $textRun->addText($data['from'] . ' – ' . ($data['to'] ?? date('Y-m-d')), array('bold' => false, 'size' => 14, 'name' => 'Times New Roman'));
        $textRun = $section->addTextRun();
        $textRun->addText("Викладач ", array('bold' => true, 'size' => 16, 'name' => 'Times New Roman'));
        $userText = $reportData->user->last_name . " " . $reportData->user->first_name . " " . $reportData->user->patronymic_name;
        $textRun->addText($userText , array('bold' => false, 'size' => 16, 'name' => 'Times New Roman'));
        $section->addText('Проведені заходи: ', array('bold' => true, 'size' => 14, 'name' => 'Times New Roman'));

        if (empty($reportData->educational_events)) {
            $section->addText('Інформація відсутня ', array('bold' => true, 'size' => 14, 'name' => 'Times New Roman'));
        } else {
            $reportData->educational_events->each(function ($event) use ($section) {
                $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
                $table->addRow();
                $table->addCell(3000)->addText('Дата проведення', array('bold' => true, 'size' => 12, 'name' => 'Times New Roman'));
                $table->addCell(6000)->addText('Назва заходу', array('bold' => true, 'size' => 12, 'name' => 'Times New Roman'));
                $table->addCell(9000)->addText('Опис заходу', array('bold' => true, 'size' => 12, 'name' => 'Times New Roman'));
                $table->addRow();
                $table->addCell(3000)->addText($event->event_date, array('bold' => false, 'size' => 12, 'name' => 'Times New Roman'));
                $table->addCell(6000)->addText($event->subject, array('bold' => false, 'size' => 12, 'name' => 'Times New Roman'));
                $table->addCell(9000)->addText($event->event_description, array('bold' => false, 'size' => 12, 'name' => 'Times New Roman'));
                $section->addText('Діти', array('bold' => true, 'size' => 12, 'name' => 'Times New Roman'));
                if ($event->children_visitors->count() > 0) {
                $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
                $table->addRow();
                $table->addCell(12000)->addText('ПІБ дитини', array('bold' => true, 'size' => 12, 'name' => 'Times New Roman'));
                $table->addCell(6000)->addText('Оцінка дитини за захід', array('bold' => true, 'size' => 12, 'name' => 'Times New Roman'));
                    $estimate_mark = 0;
                    $event->children_visitors->each(function ($child) use ($table, &$estimate_mark) {
                        $table->addRow();
                        $table->addCell(12000)->addText($child->user->last_name . " " . $child->user->first_name . " " . $child->user->patronymic_name, array('bold' => true, 'size' => 12, 'name' => 'Times New Roman'));
                        $table->addCell(6000)->addText($child->pivot->estimation_mark, array('bold' => false, 'size' => 12, 'name' => 'Times New Roman'));
                        $estimate_mark += $child->pivot->estimation_mark;
                    });
                    $avgMark = round($estimate_mark / $event->children_visitors->count(), 2);
                    $textRun = $section->addTextRun();
                    $textRun->addText('Середня оцінка - ', array('bold' => true, 'size' => 12, 'name' => 'Times New Roman'));
                    $textRun->addText($avgMark, array('bold' => true, 'size' => 12, 'name' => 'Times New Roman'));
                } else {
                    $section->addText('ІНФОРМАЦІЯ ПРО ДІТЕЙ ВІДСУТНЯ', array('bold' => true, 'size' => 12, 'name' => 'Times New Roman'));
                }
            });

        }
        $fileName = "reports/wordreports/". date('Y_m_d_H_i_s') . "_educational_events_report.docx";
        $filePath = storage_path("app/public/{$fileName}");
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        $phpWord->save($filePath, 'Word2007');
        return $filePath;
    }

    public function getDashboardEducationalEventsReportExcel(array $data): string
    {
        $reportData = $this->dashboardRepository->getDashboardEducationalEventsReportData($data);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', "Звіт Про проведені навчальні заходи");
        $sheet->setCellValue('B3', "Дата оформлення звіту: " . date('Y-m-d'));
        $sheet->setCellValue('C3', "Викладач  ");
        $userText = $reportData->user->last_name . " " . $reportData->user->first_name . " " . $reportData->user->patronymic_name;
        $sheet->setCellValue('D3', $userText);
        $sheet->setCellValue('C5', "Підзвітний період: " . $data['from'] . ' – ' . ($data['to'] ?? date('Y-m-d')));
        $sheet->setCellValue('B7', "Проведені заходи: " );
        $rowCounter = 9;
        if (empty($reportData->educational_events)) {
            $sheet->setCellValue("A{$rowCounter}", 'Інформація відсутня ');
        } else {
            $sheet->setCellValue( "A{$rowCounter}", 'Дата проведення');
            $sheet->setCellValue( "B{$rowCounter}", 'Назва заходу');
            $sheet->setCellValue( "C{$rowCounter}", 'Опис заходу');
            $sheet->setCellValue( "D{$rowCounter}", 'Діти');
            $sheet->setCellValue( "E{$rowCounter}", 'Середня оцінка');
            $rowCounter++;
            $reportData->educational_events->each(function ($event) use ($sheet, &$rowCounter) {

                $sheet->setCellValue( "A{$rowCounter}", $event->event_date);
                $sheet->setCellValue( "B{$rowCounter}", $event->subject);
                $sheet->setCellValue( "C{$rowCounter}", $event->event_description);
                if ($event->children_visitors->count() > 0) {
                    $estimate_mark = 0;
                    $childrenText = '';
                    $event->children_visitors->each(function ($child) use (&$estimate_mark, &$childrenText) {
                        $childrenText .=  $child->user->last_name . " " . $child->user->first_name . " " . $child->user->patronymic_name . " - ";
                        $childrenText .=  $child->pivot->estimation_mark . "\n ";
                        $estimate_mark += $child->pivot->estimation_mark;
                    });
                    $sheet->setCellValue("D{$rowCounter}", $childrenText);
                    $sheet->getStyle("D{$rowCounter}")->getAlignment()->setWrapText(true);
                    $avgMark = round($estimate_mark / ($event->children_visitors->count() !== 0 ? $event->children_visitors->count() : 1), 2);
                    $sheet->setCellValue("E{$rowCounter}", $avgMark);
                } else {
                    $sheet->setCellValue( "D{$rowCounter}", 'ІНФОРМАЦІЯ ПРО ДІТЕЙ ВІДСУТНЯ');
                }
                $rowCounter++;
            });
        }
        $fileName = "reports/xlsreports/". date('Y_m_d_H_i_s') . "_educational_events_report.xlsx";
        $filePath = storage_path("app/public/{$fileName}");
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        return $filePath;
    }

    public function getDashboardChildrenReportWord(array $data): string
    {
        $reportData = $this->dashboardRepository->getDashboardChildrenReportData($data);
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $section->addText("Звіт \"Про прогрес вихованців групи\"", array('bold' => true, 'size' => 16, 'name' => 'Times New Roman'));
        $textRun = $section->addTextRun();
        $textRun->addText('Дата оформлення звіту: ', array('bold' => true, 'size' => 14, 'name' => 'Times New Roman'));
        $textRun->addText(date('Y-m-d'), array('bold' => false, 'size' => 14, 'name' => 'Times New Roman'));
        $textRun = $section->addTextRun();
        $textRun->addText('Підзвітний період: ', array('bold' => true, 'size' => 14, 'name' => 'Times New Roman'));
        $textRun->addText($data['from'] . ' – ' . ($data['to'] ?? date('Y-m-d')), array('bold' => false, 'size' => 14, 'name' => 'Times New Roman'));
        $textRun = $section->addTextRun();
        $textRun->addText('Назва групи – ', array('bold' => true, 'size' => 14, 'name' => 'Times New Roman'));
        $textRun->addText( $reportData->title, array('bold' => false, 'size' => 14, 'name' => 'Times New Roman'));
        $section->addText('Вихователі групи: ', array('bold' => true, 'size' => 14, 'name' => 'Times New Roman'));
        if (!empty($reportData->teachers)) {
            $reportData->teachers->each(function ($teacher) use ($section) {
                $teacherText = $teacher->user->last_name . " " . $teacher->user->first_name . " " . $teacher->user->patronymic_name;
                $section->addListItem($teacherText, 0, array('bold' => false, 'size' => 14, 'name' => 'Times New Roman'));
            });
        }
        $section->addText('Діти: ', array('bold' => true, 'size' => 14, 'name' => 'Times New Roman'));
        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
        $table->addRow();
        $table->addCell(1000)->addText('№', array('bold' => true, 'size' => 12, 'name' => 'Times New Roman'));
        $table->addCell()->addText('ПІБ дитини', array('bold' => true, 'size' => 12, 'name' => 'Times New Roman'));
        $table->addCell()->addText('Кількість відвіданих учнем занять', array('bold' => true, 'size' => 12, 'name' => 'Times New Roman'));
        $table->addCell()->addText('Індивідуальний прогрес, %', array('bold' => true, 'size' => 12, 'name' => 'Times New Roman'));
        $totalprogres = 0;
        if (!empty($reportData->children)) {
            $reportData->children->each(function ($child, $index) use ($table, &$totalprogres) {
                $table->addRow();
                $table->addCell(1000)->addText($index + 1, array('bold' => false, 'size' => 12, 'name' => 'Times New Roman'));
                $childText = $child->user->last_name . " " . $child->user->first_name . " " . $child->user->patronymic_name;
                $table->addCell()->addText($childText, array('bold' => false, 'size' => 12, 'name' => 'Times New Roman'));
                $visited_educational_events_count = $child->visited_educational_events->count();
                $individualprogress = 0;
                if ($visited_educational_events_count > 0) {
                    $statinfo = $child->visited_educational_events->avg('pivot.estimation_mark');
                    $individualprogress = round($statinfo * 20, 2);
                }
                $table->addCell()->addText($visited_educational_events_count, array('bold' => false, 'size' => 12, 'name' => 'Times New Roman'));
                $table->addCell()->addText($individualprogress, array('bold' => false, 'size' => 12, 'name' => 'Times New Roman'));
                $totalprogres += $individualprogress;
            });
            $totalprogres = round($totalprogres / $reportData->children->count(), 2);
        }
        $textRun = $section->addTextRun();
        $textRun->addText('Загальний середній прогрес всіх учнів: ', array('bold' => true, 'size' => 14, 'name' => 'Times New Roman'));
        $textRun->addText($totalprogres . " %", array('bold' => true, 'size' => 14, 'name' => 'Times New Roman'));
        $fileName = "reports/wordreports/". date('Y_m_d_H_i_s') . "_children_report.docx";
        $filePath = storage_path("app/public/{$fileName}");
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        $phpWord->save($filePath, 'Word2007');
        return $filePath;
    }

    public function getDashboardChildrenReportExcel(array $data): string
    {
        $reportData = $this->dashboardRepository->getDashboardChildrenReportData($data);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', "Звіт \"Про прогрес вихованців групи\"");
        $sheet->setCellValue('B3', "Назва групи – " . $reportData->title);
        $sheet->setCellValue('D3', "Дата оформлення звіту: " . date('Y-m-d'));
        $sheet->setCellValue('D5', "Підзвітний період: " . $data['from'] . ' – ' . ($data['to'] ?? date('Y-m-d')));
        $sheet->setCellValue('B7', "Вихователі групи: " );
        $rowCounter = 9;
        if (!empty($reportData->teachers)) {
            $reportData->teachers->each(function ($teacher) use ($sheet, &$rowCounter) {
                $teacherText = $teacher->user->last_name . " " . $teacher->user->first_name . " " . $teacher->user->patronymic_name;
                $sheet->setCellValue( "D{$rowCounter}", $teacherText);
                $rowCounter++;
            });
            $rowCounter++;
        }
        $sheet->setCellValue( "B{$rowCounter}", 'Діти: ');
        $rowCounter++;
        $sheet->setCellValue( "A{$rowCounter}", '№');
        $sheet->setCellValue( "B{$rowCounter}", 'ПІБ дитини');
        $sheet->setCellValue( "C{$rowCounter}", 'Кількість відвіданих учнем занять');
        $sheet->setCellValue( "D{$rowCounter}", 'Індивідуальний прогрес, %');
        $rowCounter++;
        $totalprogres = 0;
        if (!empty($reportData->children)) {
            $reportData->children->each(function ($child, $index) use ($sheet, &$rowCounter, $totalprogres) {
                $sheet->setCellValue( "A{$rowCounter}",$index + 1);
                $childText = $child->user->last_name . " " . $child->user->first_name . " " . $child->user->patronymic_name;
                $sheet->setCellValue( "B{$rowCounter}",$childText);
                $visited_educational_events_count = $child->visited_educational_events->count();
                $individualprogress = 0;
                $sheet->setCellValue( "C{$rowCounter}", $visited_educational_events_count);
                if ($visited_educational_events_count > 0) {
                    $statinfo = $child->visited_educational_events->avg('pivot.estimation_mark');
                    $individualprogress = round($statinfo * 20, 2);
                }
                $sheet->setCellValue( "D{$rowCounter}", $individualprogress);
                $totalprogres += $individualprogress;
                $rowCounter++;
            });
            $totalprogres = round($totalprogres / $reportData->children->count(), 2);
        }
        $rowCounter++;
        $sheet->setCellValue( "C{$rowCounter}", 'Загальний середній прогрес всіх учнів, %:');
        $sheet->setCellValue( "D{$rowCounter}", $totalprogres );
        $fileName = "reports/xlsreports/". date('Y_m_d_H_i_s') . "_children_report.xlsx";
        $filePath = storage_path("app/public/{$fileName}");
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        return $filePath;
    }
}
