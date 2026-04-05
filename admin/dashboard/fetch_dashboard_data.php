<?php
include '../../db_connect.php'; // Your database connection

// Fetch the total number of students
$studentQuery = "SELECT COUNT(student_id) AS total_students FROM student";
$studentResult = $conn->query($studentQuery);
$totalStudents = 0;

if ($studentResult->num_rows > 0) {
    $row = $studentResult->fetch_assoc();
    $totalStudents = $row['total_students'];
}

// Fetch the number of students per course
$courseQuery = "SELECT course, COUNT(student_id) AS student_count FROM student GROUP BY course";
$courseResult = $conn->query($courseQuery);

$courseData = [];
if ($courseResult->num_rows > 0) {
    while ($row = $courseResult->fetch_assoc()) {
        $courseData[] = [
            'course' => $row['course'],
            'student_count' => $row['student_count']
        ];
    }
}

// Fetch the total number of instructors
$instructorQuery = "SELECT COUNT(prof_id) AS total_instructors FROM instructor";
$instructorResult = $conn->query($instructorQuery);
$totalInstructors = 0;

if ($instructorResult->num_rows > 0) {
    $row = $instructorResult->fetch_assoc();
    $totalInstructors = $row['total_instructors'];
}

// Fetch the number of instructors per faculty
$facultyQuery = "SELECT faculty, COUNT(prof_id) AS instructor_count FROM instructor GROUP BY faculty";
$facultyResult = $conn->query($facultyQuery);

$facultyData = [];
if ($facultyResult->num_rows > 0) {
    while ($row = $facultyResult->fetch_assoc()) {
        $facultyData[] = [
            'faculty' => $row['faculty'],
            'instructor_count' => $row['instructor_count']
        ];
    }
}

// Prepare the JSON response
$response = [
    'total_students' => $totalStudents,
    'courses' => $courseData,
    'total_instructors' => $totalInstructors,
    'faculties' => $facultyData
];

header('Content-Type: application/json');
echo json_encode($response);
