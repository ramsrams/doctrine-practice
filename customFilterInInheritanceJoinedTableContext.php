<?php

/**
 * Base Abstract Entity - Activity with three types
 * @ORM\MappedSuperclass()
 * @ORM\Entity()
 * @ORM\Table("activity")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="activity_type", fieldName="activity_type", type="string")
 * @ORM\DiscriminatorMap(
 *     {
 *          "call"="CallActivity",
 *          "task"="TaskActivity",
 *          "email"="EmailActivity"
 *     }
 * )
 */

namespace <<yourNamespace>>;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;



/**
 * Class ActivitySubjectCustomFilter applied on a SuperMapClass (Joined type inheritance ) 
 *
 * 
 *
 */
final class ActivitySubjectCustomFilter extends AbstractContextAwareFilter
{

    /**
     * {@inheritDoc}
     *
     * in this example fieldName = subject
     */
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        // otherwise filter is applied to order and page as well
        if (!$property === '<<fieldName>>') {
            return;
        }

        $parameterName = $queryNameGenerator->generateParameterName($property); // Generate a unique parameter name to avoid collisions with other filters

        $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->in(
                        'o.id',
                        'select call.id from Entity\CallActivity call where call.subject like :value'
                    ),
                    $queryBuilder->expr()->in(
                        'o.id',
                        'select task.id from Entity\TaskActivity task where task.subject like :value'
                    )
                )

            )->setParameter('value', '%'.$value.'%');

// query without query builder expressions
//           ->andWhere(
//               "o.id IN (select call_activity.id from Entity\CallActivity call_activity where call_activity.subject like '%?{$parameterName}%')"

//	whenever is needed to select a type of 
//            "SELECT activity FROM Entity\Activity activity
//            WHERE (activity INSTANCE OF Entity\CallActivity
//            OR activity INSTANCE OF Entity\TaskActivity");
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription(string $resourceClass): array
    {
        if (!$this->properties) {
            return [];
        }

        $description = [];
        foreach ($this->properties as $property => $strategy) {
            $description["activity_custom_$property"] = [
                'property' => $property,
                'type' => 'string',
                'required' => false,
                'swagger' => [
                    'description' => 'Filter by <<fieldName>>',
                    'name' => '<<fieldName>>',
                    'type' => 'string',
                ],
            ];
        }

        return $description;
    }
}