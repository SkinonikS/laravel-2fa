<?php

namespace SkinonikS\Laravel\TwoFactorAuth\Conditions;

use Illuminate\Http\Request;
use SkinonikS\Laravel\TwoFactorAuth\Token\TokenInterface;

class AggregateCondition implements ConditionInterface
{
    /**
     * @var bool
     */
    protected bool $onFirstSuccess = false;

    /**
     * @param array $conditions 
     */
    public function __construct(
        protected array $conditions,
    )
    {
        foreach ($conditions as $condition) {
            $this->add($condition);
        }
    }

    /** 
     * {@inheritDoc}
     */
    public function shouldStart(Request $request, TokenInterface $token): bool
    {
        $result = true;

        foreach ($this->conditions as $condition) {
            $result = $condition->shouldStart($request, $token);

            if ($this->onFirstSuccess && $result) {
                break;
            }
        }

        return $result;
    }

    /**
     * @param \SkinonikS\Laravel\TwoFactorAuth\Conditions\ConditionInterface $condition 
     * @return \SkinonikS\Laravel\TwoFactorAuth\Conditions\AggregateCondition 
     */
    public function add(ConditionInterface $condition): self
    {
        $this->conditions[$condition::class] = $condition;

        return $this;
    }

    /**
     * @param bool $onFirstSuccess 
     * @return \SkinonikS\Laravel\TwoFactorAuth\AggregateCondition 
     */
    public function onFirstSuccess(bool $onFirstSuccess = true): self
    {
        $this->onFirstSuccess = $onFirstSuccess;
        
        return $this;
    }
}