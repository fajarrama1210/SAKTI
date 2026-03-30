# System Prompt & Project Blueprint: Sistem Informasi Sekolah (SIS)

## 1. Architectural Constraints (STRICT RULES - DO NOT VIOLATE)
This Laravel project uses a strict Clean Architecture pattern. You MUST adhere to these rules for all new features and refactoring:
- **Controllers:** Must remain thin. They only handle HTTP Requests, call `FormRequest` for validation, call the appropriate `UseCase`, and return a View or Redirect. **NEVER use `DB::table()`, `Model::query()`, or any direct database logic in Controllers.**
- **Form Requests:** All validation rules and authorization logic must be placed in `app/Http/Requests/`.
- **UseCases:** ALL business logic, complex data transformations, and database transactions (`DB::beginTransaction()`) MUST be implemented inside `app/UseCases/`. Always return an array (e.g., `['status' => true, 'message' => '...']`) from UseCases.
- **Entities:** Constants for table names and system messages must be centralized in `app/Entities/`.

## 2. Current Project State (Phase 0)
- **Majors & Classrooms:** Basic CRUD implemented. Needs UI polish (search, filter, pagination).
- **Students Master:** Implemented with Dual-Insert logic (creates Student and User account simultaneously) and Excel Import. Needs edge-case validation for imports.

## 3. Immediate Focus: Phase 1 (Admin Panel & Finance Module)
Your current priority is to build the Finance and Admin modules. Please execute the following features step-by-step:
1. **SPP/Tuition Fee Master:** Create CRUD for configuring tuition fees based on cohorts/majors.
2. **"1 KK Banyak Anak" Logic:** Develop a detection system for students sharing the same Family Card Number (`family_card_number`). The system must support billing adjustments/discounts for siblings.
3. **Cashier/Transaction Module:** Build an interface to accept SPP payments, detect arrears, and print receipts.
4. **Academic Year Management:** Auto-promote students and generate new billing cycles.

## 4. Future Roadmap (For Schema Context Only)
Keep these future features in mind when designing the database schema, but do not build them yet:
- **Phase 2:** Student/Parent Portal for viewing bills and downloading receipts.
- **Phase 3:** Payment Gateway integration (Midtrans) and WhatsApp notifications for billing/arrears.

## Execution Instruction
Before writing any code for Phase 1, analyze the current codebase to understand the `UseCase` pattern. Then, generate an **Artifact** containing the proposed Entity-Relationship Diagram (ERD) and table schema for the Finance modules. Wait for human approval before running any migrations.