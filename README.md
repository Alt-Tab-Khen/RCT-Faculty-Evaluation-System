# RCT Faculty Evaluation System

Web-based faculty evaluation system built for Rizal College of Taal. Handles end-of-semester instructor evaluations with role-based access and automated reporting.

## Features

- **Three-tier authentication**: Admin, Instructor, and Student roles with separate dashboards
- **Data visualization**: Charts and graphs on all dashboards for evaluation metrics and trends
- **Evaluation management**: Students evaluate instructors, instructors view aggregated feedback
- **Admin controls**: Faculty management, evaluation period configuration, questionnaire creation, evaluation monitoring, report generation
- **Email integration**: PHPMailer for faculty list distribution and notifications
- **Comment filtering**: Profanity filter with blurred display and optional reveal button for instructors
- **Printable reports**: Generate PDF/printable evaluation reports after evaluation period closes

## Tech Stack

- PHP (backend logic)
- MySQL (database)
- PHPMailer (email functionality via Composer)
- CSS (styling)
- XAMPP (local development environment)

## Project Context

Academic project for Software Engineering course at RCT. Built to digitize the manual faculty evaluation process previously done via paper forms.

Traditionally, evaluations were conducted manually, leading to delays, misplaced forms, and inefficient data handling. This system provides a centralized, secure platform for conducting evaluations, storing results, and generating reports.
