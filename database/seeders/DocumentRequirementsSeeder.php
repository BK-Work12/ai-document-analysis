<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Document Requirements Seeder
 * 
 * Seeds the required documents for business loan applications
 */
class DocumentRequirementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $requirements = [
            // Company Documents - Financial
            [
                'doc_type' => 'financial_statements_year_1',
                'description' => 'Financial Statements (Year 1)',
                'required' => true,
                'active' => true,
                'sort_order' => 1,
            ],
            [
                'doc_type' => 'financial_statements_year_2',
                'description' => 'Financial Statements (Year 2)',
                'required' => true,
                'active' => true,
                'sort_order' => 2,
            ],
            [
                'doc_type' => 'financial_statements_year_3',
                'description' => 'Financial Statements (Year 3)',
                'required' => true,
                'active' => true,
                'sort_order' => 3,
            ],
            [
                'doc_type' => 'interim_financial_statements',
                'description' => 'Interim Financial Statements',
                'required' => false,
                'active' => true,
                'sort_order' => 4,
            ],

            // Company Documents - Banking & Cash Flow
            [
                'doc_type' => 'bank_statements',
                'description' => 'Bank Statements (Year 1+ interim months)',
                'required' => true,
                'active' => true,
                'sort_order' => 5,
            ],

            // Company Documents - Legal & Ownership
            [
                'doc_type' => 'articles_of_incorporation',
                'description' => 'Articles of Incorporation',
                'required' => true,
                'active' => true,
                'sort_order' => 6,
            ],
            [
                'doc_type' => 'certificate_of_incorporation',
                'description' => 'Certificate of Incorporation',
                'required' => true,
                'active' => true,
                'sort_order' => 7,
            ],
            [
                'doc_type' => 'shareholder_registry',
                'description' => 'Shareholder Registry',
                'required' => true,
                'active' => true,
                'sort_order' => 8,
            ],

            // Company Documents - Tax & Compliance
            [
                'doc_type' => 'notice_of_assessment',
                'description' => 'Notice of Assessment (Screenshot from CRA)',
                'required' => true,
                'active' => true,
                'sort_order' => 9,
            ],
            [
                'doc_type' => 't2_corporate_tax_return',
                'description' => 'T2 Corporate Tax Return (not needed if NoA is available)',
                'required' => false,
                'active' => true,
                'sort_order' => 10,
            ],

            // Company Documents - Operating
            [
                'doc_type' => 'lease_agreements',
                'description' => 'Lease Agreements',
                'required' => false,
                'active' => true,
                'sort_order' => 11,
            ],
            
            // Personal Documents - Financial Position
            [
                'doc_type' => 'personal_statement_of_affairs',
                'description' => 'Personal Statement of Affairs (PSOA)',
                'required' => true,
                'active' => true,
                'sort_order' => 12,
            ],

            // Personal Documents - Identity
            [
                'doc_type' => 'drivers_license',
                'description' => 'Driver\'s License (to validate people on corporation)',
                'required' => true,
                'active' => true,
                'sort_order' => 13,
            ],

            // Personal Documents - Credit
            [
                'doc_type' => 'credit_score_screenshot',
                'description' => 'Credit Score Screenshot',
                'required' => true,
                'active' => true,
                'sort_order' => 14,
            ],
        ];

        DB::table('document_requirements')->upsert(
            collect($requirements)
                ->map(fn ($row) => array_merge($row, ['created_at' => $now, 'updated_at' => $now]))
                ->all(),
            ['doc_type'],
            ['description', 'required', 'active', 'sort_order', 'updated_at']
        );
    }
}
