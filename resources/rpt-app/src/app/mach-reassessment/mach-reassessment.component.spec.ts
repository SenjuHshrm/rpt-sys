import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MachReassessmentComponent } from './mach-reassessment.component';

describe('MachReassessmentComponent', () => {
  let component: MachReassessmentComponent;
  let fixture: ComponentFixture<MachReassessmentComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MachReassessmentComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MachReassessmentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
