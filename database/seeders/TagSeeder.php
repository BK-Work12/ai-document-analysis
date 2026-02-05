<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Tag Seeder
 * 
 * Seeds document tags for classification and status tracking
 */
class TagSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the seeder.
     */
    public function run(): void
    {
        $tags = [
            // Document Classification Tags
            [
                'name' => 'Financial Statements',
                'slug' => 'financial-statements',
                'description' => 'Financial statement documents',
                'color' => '#3b82f6',
                'category' => 'document_type',
                'order' => 1,
            ],
            [
                'name' => 'Bank Statements',
                'slug' => 'bank-statements',
                'description' => 'Bank statement documents',
                'color' => '#10b981',
                'category' => 'document_type',
                'order' => 2,
            ],
            [
                'name' => 'Legal Documents',
                'slug' => 'legal-documents',
                'description' => 'Legal documents (articles, certificates, etc)',
                'color' => '#f59e0b',
                'category' => 'document_type',
                'order' => 3,
            ],
            [
                'name' => 'Tax Documents',
                'slug' => 'tax-documents',
                'description' => 'Tax returns and notices',
                'color' => '#ef4444',
                'category' => 'document_type',
                'order' => 4,
            ],
            [
                'name' => 'Personal Documents',
                'slug' => 'personal-documents',
                'description' => 'Personal financial and ID documents',
                'color' => '#8b5cf6',
                'category' => 'document_type',
                'order' => 5,
            ],

            // Status Tags
            [
                'name' => 'Auto-Reviewed',
                'slug' => 'auto-reviewed',
                'description' => 'Document has been auto-reviewed',
                'color' => '#06b6d4',
                'category' => 'status',
                'order' => 1,
            ],
            [
                'name' => 'Approved',
                'slug' => 'approved',
                'description' => 'Document approved by admin',
                'color' => '#22c55e',
                'category' => 'status',
                'order' => 2,
            ],
            [
                'name' => 'Pending Review',
                'slug' => 'pending-review',
                'description' => 'Awaiting manual review',
                'color' => '#f59e0b',
                'category' => 'status',
                'order' => 3,
            ],
            [
                'name' => 'Needs Revision',
                'slug' => 'needs-revision',
                'description' => 'Document requires revision or resubmission',
                'color' => '#ef4444',
                'category' => 'status',
                'order' => 4,
            ],
            [
                'name' => 'Rejected',
                'slug' => 'rejected',
                'description' => 'Document has been rejected',
                'color' => '#dc2626',
                'category' => 'status',
                'order' => 5,
            ],

            // Confidence Tags
            [
                'name' => 'High Confidence',
                'slug' => 'high-confidence',
                'description' => 'Analysis confidence >= 85%',
                'color' => '#22c55e',
                'category' => 'confidence',
                'order' => 1,
            ],
            [
                'name' => 'Medium Confidence',
                'slug' => 'medium-confidence',
                'description' => 'Analysis confidence 70-84%',
                'color' => '#eab308',
                'category' => 'confidence',
                'order' => 2,
            ],
            [
                'name' => 'Low Confidence',
                'slug' => 'low-confidence',
                'description' => 'Analysis confidence < 70%',
                'color' => '#ef4444',
                'category' => 'confidence',
                'order' => 3,
            ],

            // Risk Tags
            [
                'name' => 'Financial Risk',
                'slug' => 'financial-risk',
                'description' => 'Financial inconsistencies or risks detected',
                'color' => '#dc2626',
                'category' => 'risk',
                'order' => 1,
            ],
            [
                'name' => 'Negative Equity',
                'slug' => 'negative-equity',
                'description' => 'Company has negative equity',
                'color' => '#991b1b',
                'category' => 'risk',
                'order' => 2,
            ],
            [
                'name' => 'Overdraft Activity',
                'slug' => 'overdraft-activity',
                'description' => 'Bank account overdraft or NSF detected',
                'color' => '#dc2626',
                'category' => 'risk',
                'order' => 3,
            ],
            [
                'name' => 'Tax Arrears',
                'slug' => 'tax-arrears',
                'description' => 'Unpaid taxes or penalties',
                'color' => '#b91c1c',
                'category' => 'risk',
                'order' => 4,
            ],
            [
                'name' => 'Revenue Decline',
                'slug' => 'revenue-decline',
                'description' => 'Year-over-year revenue decline detected',
                'color' => '#f87171',
                'category' => 'risk',
                'order' => 5,
            ],
            [
                'name' => 'Low Credit Score',
                'slug' => 'low-credit-score',
                'description' => 'Personal credit score below 650',
                'color' => '#ef4444',
                'category' => 'risk',
                'order' => 6,
            ],
            [
                'name' => 'High Leverage',
                'slug' => 'high-leverage',
                'description' => 'High debt-to-equity ratio',
                'color' => '#dc2626',
                'category' => 'risk',
                'order' => 7,
            ],

            // Data Quality Tags
            [
                'name' => 'Complete Data',
                'slug' => 'complete-data',
                'description' => 'All required fields present',
                'color' => '#10b981',
                'category' => 'data_quality',
                'order' => 1,
            ],
            [
                'name' => 'Missing Data',
                'slug' => 'missing-data',
                'description' => 'Some required fields are missing',
                'color' => '#f59e0b',
                'category' => 'data_quality',
                'order' => 2,
            ],
            [
                'name' => 'Inconsistencies',
                'slug' => 'inconsistencies',
                'description' => 'Data inconsistencies detected',
                'color' => '#ef4444',
                'category' => 'data_quality',
                'order' => 3,
            ],
            [
                'name' => 'Verified',
                'slug' => 'verified',
                'description' => 'Data has been verified',
                'color' => '#10b981',
                'category' => 'data_quality',
                'order' => 4,
            ],

            // Other Tags
            [
                'name' => 'Requires Manual Review',
                'slug' => 'requires-manual-review',
                'description' => 'Should be reviewed by human analyst',
                'color' => '#f59e0b',
                'category' => 'action',
                'order' => 1,
            ],
            [
                'name' => 'Ready for Approval',
                'slug' => 'ready-for-approval',
                'description' => 'Ready for final approval',
                'color' => '#10b981',
                'category' => 'action',
                'order' => 2,
            ],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(
                ['slug' => $tag['slug']],
                $tag
            );
        }
    }
}
